HARgen
=============================

The HARgen component takes a `*.har` file and extracts the endpoints (URLs, parameters, etc.) to generate suitable configuration files for the fuzzer(s).

Place the HAR file to be analyzed in `../fuzzer/resources/`. If you used the crawler, copy the `output.har` to the resources-folder.

HARgen provides many options to filter wanted or unwanted requests or parameters for which fuzzing configurations should be created. 

Edit `hargen.sh` include the necessary arguments to `hargen.py`.
For example, one might want to fuzz static files, so pass `-use '.jpg,.png'`.
Change `--har-path` to point to the correct HAR file you want to have analyzed.

Then, run `docker-compose up hargen --build --force-recreate` from the parent directory.
Finally, you should find the generated configuration files in `fuzzer/configs:

```
$ ls fuzzer/configs/*.json
-rw-r--r-- 1 root root 1.6K Apr  7 14:50 fuzzer/configs/hargen_0001.json
-rw-r--r-- 1 root root 1.8K Apr  7 14:50 fuzzer/configs/hargen_0007.json
-rw-r--r-- 1 root root 2.4K Apr  7 14:50 fuzzer/configs/hargen_0013.json
-rw-r--r-- 1 root root 2.0K Apr  7 14:50 fuzzer/configs/hargen_0019.json
-rw-r--r-- 1 root root 1.9K Apr  7 14:50 fuzzer/configs/hargen_0025.json
-rw-r--r-- 1 root root 2.5K Apr  7 14:50 fuzzer/configs/hargen_0032.json
```

```
usage: hargen.py [-h] (-hd HAR_DIR | -hp HAR_PATH) [-ls LOGIN_SCRIPT] [-lc LOGIN_COOKIES] [-uf URL_FUZZER] [-upi URL_PREFIX_INCLUDE] [-usi URL_SUFFIX_INCLUDE] [-umi URL_METHODS_INCLUDE] [-upe URL_PREFIX_EXCLUDE] [-use URL_SUFFIX_EXCLUDE] [-ume URL_METHODS_EXCLUDE] [-fi] [-fa]
                 [-fes] [-fha] [-fca] [-fqa] [-fba] [-fhi FUZZ_HEADERS_INCLUDE] [-fci FUZZ_COOKIES_INCLUDE] [-fqi FUZZ_QUERY_INCLUDE] [-fbi FUZZ_BODY_INCLUDE] [-fhe FUZZ_HEADERS_EXCLUDE] [-fce FUZZ_COOKIES_EXCLUDE] [-fqe FUZZ_QUERY_EXCLUDE] [-fbe FUZZ_BODY_EXCLUDE]
                 [-shi SET_HEADERS_INCLUDE] [-sci SET_COOKIES_INCLUDE] [-sqi SET_QUERY_INCLUDE] [-sbi SET_BODY_INCLUDE] [-she SET_HEADERS_EXCLUDE] [-sce SET_COOKIES_EXCLUDE] [-sqe SET_QUERY_EXCLUDE] [-sbe SET_BODY_EXCLUDE] [-op OUT_PREFIX] -od OUT_DIR

HAR to Phuzz config generator

options:
  -h, --help            show this help message and exit
  -hd HAR_DIR, --har-dir HAR_DIR
                        Path to directory containing .har files to process
  -hp HAR_PATH, --har-path HAR_PATH
                        Path to .har file

Target options:
  -ls LOGIN_SCRIPT, --login-script LOGIN_SCRIPT
                        The login script to execute.
  -lc LOGIN_COOKIES, --login-cookies LOGIN_COOKIES
                        Comma separated list oflogin-session cookie names.
  -uf URL_FUZZER, --url-fuzzer URL_FUZZER
                        The URL part that replaces the target url part to match the Phuzz docker environment
  -upi URL_PREFIX_INCLUDE, --url-prefix-include URL_PREFIX_INCLUDE
                        Comma separated list of URL prefixes to include
  -usi URL_SUFFIX_INCLUDE, --url-suffix-include URL_SUFFIX_INCLUDE
                        Comma separated list of URL suffixes to include
  -umi URL_METHODS_INCLUDE, --url-methods-include URL_METHODS_INCLUDE
                        Comma separated list of URL methods to include
  -upe URL_PREFIX_EXCLUDE, --url-prefix-exclude URL_PREFIX_EXCLUDE
                        Comma separated list of URL prefixes to exclude
  -use URL_SUFFIX_EXCLUDE, --url-suffix-exclude URL_SUFFIX_EXCLUDE
                        Comma separated list of URL suffixes to exclude
  -ume URL_METHODS_EXCLUDE, --url-methods-exclude URL_METHODS_EXCLUDE
                        Comma separated list of URL methods to exclude

Fuzzing options:
  -fi, --fuzz-interactive
                        Go through the requests and options in an interactive way.
  -fa, --fuzz-all       Fuzz all parameters (headers, cookies, query, body)
  -fes, --fuzz-empty-seeds
                        Fuzz the parameters with empty seeds. Otherwise use the parameters's value
  -fha, --fuzz-headers  Fuzz all header parameters
  -fca, --fuzz-cookies  Fuzz all cookie parameters
  -fqa, --fuzz-query    Fuzz all query parameters
  -fba, --fuzz-body     Fuzz all body parameters

Fuzzing parameters:
  -fhi FUZZ_HEADERS_INCLUDE, --fuzz-headers-include FUZZ_HEADERS_INCLUDE
                        Comma separated list of headers to be fuzzed.
  -fci FUZZ_COOKIES_INCLUDE, --fuzz-cookies-include FUZZ_COOKIES_INCLUDE
                        Comma separated list of cookies to be fuzzed.
  -fqi FUZZ_QUERY_INCLUDE, --fuzz-query-include FUZZ_QUERY_INCLUDE
                        Comma separated list of query to be fuzzed.
  -fbi FUZZ_BODY_INCLUDE, --fuzz-body-include FUZZ_BODY_INCLUDE
                        Comma separated list of body to be fuzzed.
  -fhe FUZZ_HEADERS_EXCLUDE, --fuzz-headers-exclude FUZZ_HEADERS_EXCLUDE
                        Comma separated list of headers to not be fuzzed
  -fce FUZZ_COOKIES_EXCLUDE, --fuzz-cookies-exclude FUZZ_COOKIES_EXCLUDE
                        Comma separated list of cookies to not be fuzzed
  -fqe FUZZ_QUERY_EXCLUDE, --fuzz-query-exclude FUZZ_QUERY_EXCLUDE
                        Comma separated list of query to not be fuzzed
  -fbe FUZZ_BODY_EXCLUDE, --fuzz-body-exclude FUZZ_BODY_EXCLUDE
                        Comma separated list of body to not be fuzzed

Set parameters:
  -shi SET_HEADERS_INCLUDE, --set-headers-include SET_HEADERS_INCLUDE
                        Comma separated list of headers to have a fixed value.
  -sci SET_COOKIES_INCLUDE, --set-cookies-include SET_COOKIES_INCLUDE
                        Comma separated list of cookies to have a fixed value.
  -sqi SET_QUERY_INCLUDE, --set-query-include SET_QUERY_INCLUDE
                        Comma separated list of query to have a fixed value.
  -sbi SET_BODY_INCLUDE, --set-body-include SET_BODY_INCLUDE
                        Comma separated list of body to have a fixed value.
  -she SET_HEADERS_EXCLUDE, --set-headers-exclude SET_HEADERS_EXCLUDE
                        Comma separated list of headers to not have a fixed value.
  -sce SET_COOKIES_EXCLUDE, --set-cookies-exclude SET_COOKIES_EXCLUDE
                        Comma separated list of cookies to not have a fixed value.
  -sqe SET_QUERY_EXCLUDE, --set-query-exclude SET_QUERY_EXCLUDE
                        Comma separated list of query to not have a fixed value.
  -sbe SET_BODY_EXCLUDE, --set-body-exclude SET_BODY_EXCLUDE
                        Comma separated list of body to not have a fixed value.

Config output:
  -op OUT_PREFIX, --out-prefix OUT_PREFIX
                        Prefix to use for generated config files
  -od OUT_DIR, --out-dir OUT_DIR
                        Output directory to write generated config files to.
```