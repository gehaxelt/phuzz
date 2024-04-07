import re
import sys
import json
from itertools import product
from collections import ChainMap
#from ConfigParser import _Chainmap as ChainMap

from urllib.parse import urlparse, parse_qsl, urlunparse
import requests

class Phuzz:

    def __init__(self, fuzzer_config_name, *args, **kwargs):
        self.fuzzer_config_name = fuzzer_config_name
        self.config = None

        self.http_methods = []
        
        self.fixed_headers = {}
        self.fuzz_headers = {}
        self.weight_headers = 0.25
        
        self.fixed_cookies = {}
        self.fuzz_cookies = {}
        self.weight_cookies = 0.25

        self.fixed_query_params = {}
        self.fuzz_query_params = {}
        self.weight_query_params = 0.25

        self.fixed_body_params = {}
        self.fuzz_body_params = {}
        self.weight_body_params = 0.25

    def _param_tuple_to_dict(self, tpl):
        return dict(ChainMap(*list(map(lambda x: {x['name']: x['value']}, tpl))))

    # Taken from fuzzer/fuzzer.py
    def load_config(self):
        try:
            self.config = json.load(
                open(self.fuzzer_config_name)
            )
        except Exception as e:
            print(e)
            sys.exit("Failed to parse fuzzer config: {self.fuzzer_config_name}")

        if not self.config["target"].startswith("http"):
            sys.exit("Target does not start with http!")

    def load_request_data(self):
        potential = {}
        potential['methods'] = []
        potential['headers'] = []
        potential['cookies'] = []
        potential['query_params'] = []
        potential['body_params'] = []

        if 'methods' in self.config:
            potential['methods'] += self.config['methods']

        self.http_methods = list(set(potential['methods']))


        for config_key in ['headers', 'cookies', 'query_params', 'body_params']:
            if not config_key in self.config:
                continue

            if "data" in self.config[config_key]:
                potential[config_key] += self.config[config_key]['data']
            else:
                raise Exception("Config parsing error: No parameters specified with 'data' for {config_key}")

            if 'weight' in self.config[config_key]:
                setattr(self, "weight_"+config_key, self.config[config_key]['weight'])

        # now filter/assign these to fixed/fuzz params
        for config_key in ['headers', 'cookies', 'query_params', 'body_params']:
            # Fixed params need a {'name': name, 'value': value} dict!
            fixed_dict = getattr(self,"fixed_"+config_key,{})
            #print("fixed dict init: ", fixed_dict)
            if config_key in self.config and 'fixed' in self.config[config_key] and self.config[config_key]['fixed']:
                for regex in self.config[config_key]['fixed']:
                    r = re.compile(regex)
                    for param in potential[config_key]:
                        param_name = param['name']
                        if r.match(param_name):
                            if not param_name in fixed_dict:
                                fixed_dict[param_name] = set()
                            if 'value' in param:
                                fixed_dict[param_name].add(param['value'])
                            elif 'seeds' in param:
                                fixed_dict[param_name].update(param['seeds'])
                            else:
                                raise Exception("Neither seeds nor value for param {param_name}")

            fuzz_dict = getattr(self,"fuzz_"+config_key,{})

            #print("fuzz dict init: ", fuzz_dict)
            if config_key in self.config and 'fuzz' in self.config[config_key] and self.config[config_key]['fuzz']:
                for regex in self.config[config_key]['fuzz']:
                    r = re.compile(regex)
                    for param in potential[config_key]:
                        param_name = param['name']
                        # Ignore fixed params that we have already set.
                        if param_name in fixed_dict:
                            continue
                        if config_key == 'headers' and param_name.lower() in ["host", "cookie"]:
                            continue
                        if r.match(param_name):
                            if not param_name in fuzz_dict:
                                fuzz_dict[param_name] = set()
                            if 'value' in param:
                                fuzz_dict[param_name].add(param['value'])
                            elif 'seeds' in param:
                                fuzz_dict[param_name].update(param['seeds'])
                            else:
                                raise Exception("Neither seeds nor value for param {param_name}")
            else:
                # Fuzz all by default
                for param in potential[config_key]:
                    param_name = param['name']
                    # Ignore fixed params that we have already set.
                    if param_name in fixed_dict:
                        continue
                    if config_key == 'headers' and param_name.lower() in ["host", "cookie"]:
                        continue
                    if not param_name in fuzz_dict:
                        fuzz_dict[param_name] = set()
                    if 'value' in param:
                        fuzz_dict[param_name].add(param['value'])
                    elif 'seeds' in param:
                        fuzz_dict[param_name].update(param['seeds'])
                    else:
                        raise Exception("Neither seeds nor value for param {param_name}")


            for k in fixed_dict:
                fixed_dict[k] = list(fixed_dict[k])
            for k in fuzz_dict:
                fuzz_dict[k] = list(fuzz_dict[k])
            setattr(self, "fuzz_"+config_key, fuzz_dict)
            setattr(self, "fixed_"+config_key, fixed_dict)

    def generate_initial_candidates(self):

        print("Fixed headers", self.fixed_headers)
        print("Fixed Cookies", self.fixed_cookies)
        print("Fixed Query Params", self.fixed_query_params)
        print("Fixed Body Params", self.fixed_body_params)

        print("Fuzz headers", self.fuzz_headers)
        print("Fuzz cookies", self.fuzz_cookies)
        print("Fuzz query params", self.fuzz_query_params)
        print("Fuzz body params", self.fuzz_body_params)

        fixed_generators = {}
        fuzz_generators = {}

        for keyword in ['headers', 'cookies', 'query_params', 'body_params']:
            fixed_dict = getattr(self, "fixed_"+keyword, {})
            keyword_comb = []
            for k in fixed_dict:
                tmp_list = []
                for v in fixed_dict[k]:
                    tmp_list.append({'name': k, 'value': v})
                keyword_comb.append(tmp_list)

            fixed_generators[keyword] = list(product(*keyword_comb))

            fuzz_dict = getattr(self, "fuzz_"+keyword, {})
            #print("fuzz dict: ", fuzz_dict)
            keyword_comb = []
            for k in fuzz_dict:
                tmp_list = []
                for v in fuzz_dict[k]:
                    tmp_list.append({'name': k, 'value': v})
                keyword_comb.append(tmp_list)

            fuzz_generators[keyword] = list(product(*keyword_comb))

        for req_method in self.http_methods:
            for fixed_header_comb in fixed_generators['headers']:
                for fixed_cookie_comb in fixed_generators['cookies']:
                    for fixed_query_params_comb in fixed_generators['query_params']:
                        for fixed_body_params_comb in fixed_generators['body_params']:
                            for fuzz_header_comb in fuzz_generators['headers']:
                                for fuzz_cookie_comb in fuzz_generators['cookies']:
                                    for fuzz_query_params_comb in fuzz_generators['query_params']:
                                        for fuzz_body_params_comb in fuzz_generators['body_params']:

                                            c = {
                                                "http_target":self.config['target'],
                                                "http_method":req_method,
                                                "fixed_params":{
                                                    'headers': self._param_tuple_to_dict(fixed_header_comb),
                                                    'cookies': self._param_tuple_to_dict(fixed_cookie_comb),
                                                    'query_params': self._param_tuple_to_dict(fixed_query_params_comb),
                                                    'body_params': self._param_tuple_to_dict(fixed_body_params_comb)
                                                },
                                                "fuzz_params":{
                                                    'headers': self._param_tuple_to_dict(fuzz_header_comb),
                                                    'cookies': self._param_tuple_to_dict(fuzz_cookie_comb),
                                                    'query_params': self._param_tuple_to_dict(fuzz_query_params_comb),
                                                    'body_params': self._param_tuple_to_dict(fuzz_body_params_comb)
                                                },
                                                "fuzz_weights":{
                                                    'headers': self.weight_headers,
                                                    'cookies': self.weight_cookies,
                                                    'query_params': self.weight_query_params,
                                                    'body_params': self.weight_body_params
                                                }
                                            }
                                            yield c

    def prepare_request(self, candidate):
        base_url = candidate['http_target']

        # based on https://stackoverflow.com/a/2506477
        url_parts = list(urlparse(base_url))
        query = dict(parse_qsl(url_parts[4]))
        url_parts[4] = '' # reset query string, which we will set using params={...}

        the_params = {}
        the_params.update(query.copy())
        the_params.update(candidate['fuzz_params']['query_params'].copy())
        the_params.update(candidate['fixed_params']['query_params'].copy())

        the_body_params = {}
        the_body_params.update(candidate['fuzz_params']['body_params'].copy())
        the_body_params.update(candidate['fixed_params']['body_params'].copy())

        the_cookies = {}
        the_cookies.update(candidate['fuzz_params']['cookies'].copy())
        the_cookies.update(candidate['fixed_params']['cookies'].copy())# self._urlencode_dict()

        the_headers = {}
        the_headers.update(candidate['fuzz_params']['headers'].copy())
        the_headers.update(candidate['fixed_params']['headers'].copy())

        # print({
        #     'query': the_params,
        #     'cookies': the_cookies,
        #     'headers': the_headers,
        #     'body': the_body_params    
        #     })

        if candidate['http_method'] in ["GET", "OPTIONS", "TRACE"]:
            req = requests.Request(method=candidate['http_method'], 
                                    url=urlunparse(url_parts),
                                    params=the_params,
                                    cookies=the_cookies,
                                    headers=the_headers)

        elif candidate['http_method'] in ["POST", "PUT", "DELETE"]:
            if the_headers.get('Content-Type', '') in ['application/json']:
                req = requests.Request(method=candidate['http_method'], 
                                    url=urlunparse(url_parts),
                                    params=the_params,
                                    cookies=the_cookies,
                                    headers=the_headers,
                                    json=the_body_params)
            else:
                req = requests.Request(method=candidate['http_method'], 
                                    url=urlunparse(url_parts),
                                    params=the_params,
                                    cookies=the_cookies,
                                    headers=the_headers,
                                    data=the_body_params)

        else:
            raise Exception("Unknown HTTP method!")

        prepared = req.prepare()

        return prepared