import argparse
import glob
import json
import os
import pprint
import sys
from urllib.parse import unquote, urlparse, urlunparse


class HARRequest():
    def __init__(self, raw_request):
        self.raw_request = raw_request
        self.request_url = raw_request.get('url', '')
        self.request_method = raw_request.get('method', '')
        self.request_headers = raw_request.get('headers', [])
        self.request_cookies = raw_request.get('cookies', [])
        self.request_query = raw_request.get('queryString', [])
        self.request_body = raw_request.get('postData', {}).get('params', [])

        self.config_url = None
        self.config_method = None
        self.config_headers = {
            'data': [],
            'fuzz': [],
            'fixed': []
        }
        self.config_cookies = {
            'data': [],
            'fuzz': [],
            'fixed': [],
            'login': []
        }
        self.config_query = {
            'data': [],
            'fuzz': [],
            'fixed': []
        }
        self.config_body = {
            'data': [],
            'fuzz': [],
            'fixed': []
        }

    def get_paramless_url(self):
        o = urlparse(self.request_url)
        if not ':80' in o.netloc or not ':443' in o.netloc:
            return f"{o.scheme}://{o.netloc}{o.path}"
        else:
            return f"{o.scheme}://{o.hostname}{o.path}"


class HARGen():

    def __init__(self, args):
        self.args = args

    def ask_yes_no(self, q):
        a = input(q + " (y/n)").strip()
        print("a is: ", a)
        if a in ['y', 'Y']:
            return True
        elif a in ['n', 'N']:
            return False
        else:
            print("I do not understand you.")
            return self.ask_yes_no(q)

    def analyze_har(self, hf):
        with open(hf) as fh:
            json_har = json.load(fh)

        har_entries = json_har['log']['entries']

        print(f"Found {len(har_entries)} requests in {hf}")

        counter = 0
        for entry in har_entries:
            counter += 1
            har_request = HARRequest(entry['request'])
            print(
                f"Checking {har_request.request_method} {har_request.request_url}")

            if self.args.url_prefix_include:
                is_included = False
                for upi in self.args.url_prefix_include.split(","):
                    if har_request.get_paramless_url().startswith(upi):
                        is_included = True
                        break
                if not is_included:
                    print("Skipping request URL due to prefix include")
                    continue

            if self.args.url_suffix_include:
                is_included = False
                for usi in self.args.url_suffix_include.split(","):
                    if har_request.get_paramless_url().endswith(usi):
                        is_included = True
                        break
                if not is_included:
                    print("Skipping request URL due to suffix include")
                    continue

            if self.args.url_prefix_exclude:
                is_excluded = False
                for upe in self.args.url_prefix_exclude.split(","):
                    if har_request.get_paramless_url().startswith(upe):
                        is_excluded = True
                        break

                if is_excluded:
                    print("Skipping request URL due to prefix exclude")
                    continue

            if self.args.url_suffix_exclude:
                is_excluded = False
                for upe in self.args.url_suffix_exclude.split(","):
                    if har_request.get_paramless_url().endswith(upe):
                        is_excluded = True
                        break

                if is_excluded:
                    print("Skipping request URL due to prefix exclude")
                    continue

            if self.args.url_fuzzer:
                print(f"Replacing URL with {self.args.url_fuzzer}.")
                ru = urlparse(har_request.request_url)
                uf = urlparse(self.args.url_fuzzer)

                new_url = []
                for i, e in enumerate(uf):
                    if uf[i]:
                        new_url.append(uf[i])
                    else:
                        new_url.append(ru[i])

                new_url[4] = ''  # We do not need the query params in the URL

                # har_request.request_url.replace(self.args.url_target, self.args.url_fuzzer)
                har_request.config_url = urlunparse(new_url)

            if self.args.url_methods_include:
                if not har_request.request_method in self.args.url_methods_include.split(","):
                    print("Skipping request due to method include")
                    continue
            if self.args.url_methods_exclude:
                if har_request.request_method in self.args.url_methods_exclude.split(","):
                    print("Skipping request due to method exclude")
                    continue

            har_request.config_method = har_request.request_method

            if self.args.fuzz_all:
                self.fuzz_headers(har_request)
                self.fuzz_cookies(har_request)
                self.fuzz_query(har_request)
                self.fuzz_body(har_request)
            else:
                if self.args.fuzz_headers:
                    self.fuzz_headers(har_request)

                if self.args.fuzz_cookies:
                    self.fuzz_cookies(har_request)

                if self.args.fuzz_query:
                    self.fuzz_query(har_request)

                if self.args.fuzz_body:
                    self.fuzz_body(har_request)

            self.set_headers(har_request)
            self.set_cookies(har_request)
            self.set_query(har_request)
            self.set_body(har_request)

            fpath = os.path.join(
                self.args.out_dir, self.args.out_prefix + '{:0>4}'.format(counter) + ".json")
            with open(fpath, "w") as w:
                config = self.generate_config(har_request)
                w.write(json.dumps(config, indent=2))

    def generate_config(self, hreq):
        config = {
            "target": hreq.config_url,
            "methods": [
                hreq.config_method
            ]
        }

        if self.args.login_script:
            config["login"] = self.args.login_script

        if hreq.config_headers['data']:
            config["headers"] = {
                'data': hreq.config_headers['data'],
                "fixed": hreq.config_headers['fixed'],
                "fuzz": hreq.config_headers['fuzz'],
                "weight": int(max(self.args.fuzz_headers, self.args.fuzz_all))
            }
        if hreq.config_cookies['data'] or hreq.config_cookies['login']:
            config["cookies"] = {
                'data': hreq.config_cookies['data'],
                "fixed": hreq.config_cookies['fixed'],
                "fuzz": hreq.config_cookies['fuzz'],
                "login": hreq.config_cookies['login'],
                "weight": int(max(self.args.fuzz_cookies, any(hreq.config_cookies['login']), self.args.fuzz_all))
            }
        if hreq.config_query['data']:
            config["query_params"] = {
                'data': hreq.config_query['data'],
                "fixed": hreq.config_query['fixed'],
                "fuzz": hreq.config_query['fuzz'],
                "weight": int(max(self.args.fuzz_query, self.args.fuzz_all))
            }

        if hreq.config_body['data']:
            config["body_params"] = {
                'data': hreq.config_body['data'],
                "fixed": hreq.config_body['fixed'],
                "fuzz": hreq.config_body['fuzz'],
                "weight": int(max(self.args.fuzz_body, self.args.fuzz_all))
            }

        pprint.pprint(config)
        return config

    def fuzz_generic(self, hreq, fuzz_type):
        fuzz_include = getattr(self.args, f"fuzz_{fuzz_type}_include", None)
        fuzz_exclude = getattr(self.args, f"fuzz_{fuzz_type}_exclude", None)

        set_include = getattr(self.args, f"set_{fuzz_type}_include", None)

        if fuzz_type == "cookies" and self.args.login_cookies:
            login_cookies = list(
                map(lambda x: x.lower(), self.args.login_cookies.split(',')))
        else:
            login_cookies = []

        if fuzz_include:
            fuzz_include_list = list(
                map(lambda x: x.lower(), fuzz_include.split(',')))
        else:
            fuzz_include_list = []

        if fuzz_exclude:
            fuzz_exclude_list = list(
                map(lambda x: x.lower(), fuzz_exclude.split(',')))
        else:
            fuzz_exclude_list = []

        if set_include:
            set_include_list = list(
                map(lambda x: x.lower(), set_include.split(',')))
        else:
            set_include_list = []

        for itm in getattr(hreq, f"request_{fuzz_type}", []):
            itmname = unquote(itm['name'])
            if self.args.fuzz_empty_seeds:
                itmvalue = ""
            else:
                itmvalue = unquote(itm['value'])

            if fuzz_type == 'headers' and itmname.lower() in ['cookie']:
                continue
            if login_cookies and itmname.lower() in login_cookies:
                if not itmname in hreq.config_cookies['login']:
                    hreq.config_cookies['login'].append(itmname)
                continue

            if itmname in getattr(hreq, f"config_{fuzz_type}", {}).get('fixed', []):
                continue

            if set_include_list and itmname.lower() in set_include_list:
                continue

            if fuzz_include_list and not itmname.lower() in fuzz_include_list:
                continue
            if fuzz_exclude_list and itmname.lower() in fuzz_exclude_list:
                continue

            getattr(hreq, f"config_{fuzz_type}", {}).get('data', []).append({
                'name': itmname,
                'value': itmvalue
            })
            getattr(hreq, f"config_{fuzz_type}", {}).get(
                'fuzz', []).append(itmname)

    def set_generic(self, hreq, fuzz_type):
        set_include = getattr(self.args, f"set_{fuzz_type}_include", None)
        set_exclude = getattr(self.args, f"set_{fuzz_type}_exclude", None)

        fuzz_include = getattr(self.args, f"fuzz_{fuzz_type}_include", None)

        if fuzz_type == "cookies" and self.args.login_cookies:
            login_cookies = list(
                map(lambda x: x.lower(), self.args.login_cookies.split(',')))
        else:
            login_cookies = []

        if set_include:
            set_include_list = list(
                map(lambda x: x.lower(), set_include.split(',')))
        else:
            set_include_list = []

        if set_exclude:
            set_exclude_list = list(
                map(lambda x: x.lower(), set_exclude.split(',')))
        else:
            set_exclude_list = []

        if fuzz_include:
            fuzz_include_list = list(
                map(lambda x: x.lower(), fuzz_include.split(',')))
        else:
            fuzz_include_list = []

        for itm in getattr(hreq, f"request_{fuzz_type}", []):
            itmname = unquote(itm['name'])
            if self.args.fuzz_empty_seeds:
                itmvalue = ""
            else:
                itmvalue = unquote(itm['value'])

            if fuzz_type == 'headers' and itmname.lower() in ['cookie']:
                continue
            if login_cookies and itmname.lower() in login_cookies:
                if not itmname in hreq.config_cookies['login']:
                    hreq.config_cookies['login'].append(itmname)
                continue

            if itmname in getattr(hreq, f"config_{fuzz_type}", {}).get('fuzz', []):
                continue
            if fuzz_include_list and itmname.lower() in fuzz_include_list:
                continue

            if set_include_list and not itmname.lower() in set_include_list:
                continue
            if set_exclude_list and itmname.lower() in set_exclude_list:
                continue

            getattr(hreq, f"config_{fuzz_type}", {}).get('data', []).append({
                'name': itmname,
                'value': itmvalue
            })
            getattr(hreq, f"config_{fuzz_type}", {}).get(
                'fixed', []).append(itmname)

    def fuzz_headers(self, hreq):
        self.fuzz_generic(hreq, "headers")

    def set_headers(self, hreq):
        self.set_generic(hreq, "headers")

    def fuzz_cookies(self, hreq):
        self.fuzz_generic(hreq, "cookies")

    def fuzz_query(self, hreq):
        self.fuzz_generic(hreq, "query")

    def fuzz_body(self, hreq):
        self.fuzz_generic(hreq, "body")

    def set_cookies(self, hreq):
        self.set_generic(hreq, "cookies")

    def set_query(self, hreq):
        self.set_generic(hreq, "query")

    def set_body(self, hreq):
        self.set_generic(hreq, "body")

    def run_hargen(self):
        har_files = []
        if self.args.har_dir:
            if not os.path.exists(self.args.har_dir):
                sys.exit(f"HAR path {self.args.har_dir} does not exist.")
            har_files = glob.glob(self.args.har_dir + "/*.har")
        elif self.args.har_path:
            if not os.path.exists(self.args.har_path):
                sys.exit(f"HAR file {self.args.har_path} does not exist.")
            har_files = [self.args.har_path]

        if self.args.out_dir:
            if not os.path.exists(self.args.out_dir):
                try:
                    os.mkdir(self.args.out_dir)
                    print(f"Created directory {self.args.out_dir}")
                except:
                    sys.exit(f"Failed to create directory {self.args.out_dir}")

        print(f"Found the {len(har_files)} har files: ")
        for har_file in har_files.copy():
            if args.fuzz_interactive and not ask_yes_no(f"Generate config for {har_file}?"):
                print(f"Removing {har_file}")
                har_files.remove(har_file)
            else:
                print(f"Adding {har_file}")

        for har_file in har_files:
            print(f"Analyzing har file {har_file}")

            self.analyze_har(har_file)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(
        description="HAR to Phuzz config generator")

    har_group = parser.add_mutually_exclusive_group(required=True)
    har_group.add_argument(
        "-hd", "--har-dir", help="Path to directory containing .har files to process")
    har_group.add_argument(
        "-hp", "--har-path", help="Path to .har file")

    target_group = parser.add_argument_group('Target options')
    target_group.add_argument('-ls', '--login-script',
                              help='The login script to execute.')
    target_group.add_argument(
        '-lc', '--login-cookies', help='Comma separated list oflogin-session cookie names.', default='PHPSESSID')
    # target_group.add_argument('-ut', '--url-target', help='The URL part that will be replaced', default='http://localhost:8080/')
    target_group.add_argument(
        '-uf', '--url-fuzzer', help='The URL part that replaces the target url part to match the Phuzz docker environment', default='http://web')
    target_group.add_argument('-upi', '--url-prefix-include',
                              help='Comma separated list of URL prefixes to include', default='http://localhost:8080/')
    target_group.add_argument('-usi', '--url-suffix-include',
                              help='Comma separated list of URL suffixes to include', default='.php,/')
    target_group.add_argument('-umi', '--url-methods-include',
                              help='Comma separated list of URL methods to include', default='GET,POST')
    target_group.add_argument('-upe', '--url-prefix-exclude',
                              help='Comma separated list of URL prefixes to exclude')
    target_group.add_argument('-use', '--url-suffix-exclude',
                              help='Comma separated list of URL suffixes to exclude', default='.gif,.png,.js,.css,.ico')
    target_group.add_argument('-ume', '--url-methods-exclude',
                              help='Comma separated list of URL methods to exclude', default='TRACE,OPTIONS')

    fuzz_group = parser.add_argument_group('Fuzzing options')
    fuzz_group.add_argument('-fi', '--fuzz-interactive',
                            help='Go through the requests and options in an interactive way.', action='store_true', default=False)
    fuzz_group.add_argument(
        '-fa', '--fuzz-all', help='Fuzz all parameters (headers, cookies, query, body)', action='store_true', default=False)
    fuzz_group.add_argument('-fes', '--fuzz-empty-seeds',
                            help='Fuzz the parameters with empty seeds. Otherwise use the parameters\'s value', action='store_true', default=False)

    fuzz_group.add_argument('-fha', '--fuzz-headers',
                            help='Fuzz all header parameters', action='store_true', default=False)
    # fuzz_group.add_argument('-sha', '--set-headers-all', help='Set all header parameters as fixed',action='store_true', default=True)

    fuzz_group.add_argument('-fca', '--fuzz-cookies',
                            help='Fuzz all cookie parameters', action='store_true', default=False)
    # fuzz_group.add_argument('-sca', '--set-cookies-all', help='Set all cookie parameters as fixed',action='store_true', default=False)

    fuzz_group.add_argument(
        '-fqa', '--fuzz-query', help='Fuzz all query parameters', action='store_true', default=False)
    # fuzz_group.add_argument('-sqa', '--set-query-all', help='Fuzz all query parameters',action='store_true', default=False)

    fuzz_group.add_argument(
        '-fba', '--fuzz-body', help='Fuzz all body parameters', action='store_true', default=False)
    # fuzz_group.add_argument('-sba', '--set-body-all', help='Set all body parameters as fixed',action='store_true', default=False)

    fuzz_params_group = parser.add_argument_group('Fuzzing parameters')
    fuzz_params_group.add_argument(
        '-fhi', '--fuzz-headers-include', help='Comma separated list of headers to be fuzzed.')
    fuzz_params_group.add_argument(
        '-fci', '--fuzz-cookies-include', help='Comma separated list of cookies to be fuzzed.')
    fuzz_params_group.add_argument(
        '-fqi', '--fuzz-query-include', help='Comma separated list of query to be fuzzed.')
    fuzz_params_group.add_argument(
        '-fbi', '--fuzz-body-include', help='Comma separated list of body to be fuzzed.')
    fuzz_params_group.add_argument('-fhe', '--fuzz-headers-exclude',
                                   help='Comma separated list of headers to not be fuzzed', default='host,cookie,connection')
    fuzz_params_group.add_argument(
        '-fce', '--fuzz-cookies-exclude', help='Comma separated list of cookies to not be fuzzed')
    fuzz_params_group.add_argument(
        '-fqe', '--fuzz-query-exclude', help='Comma separated list of query to not be fuzzed')
    fuzz_params_group.add_argument(
        '-fbe', '--fuzz-body-exclude', help='Comma separated list of body to not be fuzzed')

    set_params_group = parser.add_argument_group('Set parameters')
    set_params_group.add_argument('-shi', '--set-headers-include',
                                  help='Comma separated list of headers to have a fixed value.')
    set_params_group.add_argument('-sci', '--set-cookies-include',
                                  help='Comma separated list of cookies to have a fixed value.')
    set_params_group.add_argument('-sqi', '--set-query-include',
                                  help='Comma separated list of query to have a fixed value.')
    set_params_group.add_argument(
        '-sbi', '--set-body-include', help='Comma separated list of body to have a fixed value.')
    set_params_group.add_argument('-she', '--set-headers-exclude',
                                  help='Comma separated list of headers to not have a fixed value.', default='host,cookie,connection')
    set_params_group.add_argument('-sce', '--set-cookies-exclude',
                                  help='Comma separated list of cookies to not have a fixed value.')
    set_params_group.add_argument('-sqe', '--set-query-exclude',
                                  help='Comma separated list of query to not have a fixed value.')
    set_params_group.add_argument(
        '-sbe', '--set-body-exclude', help='Comma separated list of body to not have a fixed value.')

    output_group = parser.add_argument_group('Config output')
    output_group.add_argument(
        '-op', '--out-prefix', help='Prefix to use for generated config files', default='hargen_')
    output_group.add_argument(
        '-od', '--out-dir', help='Output directory to write generated config files to.', default='/configs/', required=True)
    args = parser.parse_args()

    hg = HARGen(args)
    hg.run_hargen()
