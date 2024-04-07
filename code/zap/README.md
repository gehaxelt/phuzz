ZAP
==============================

We use the Zend Attack Proxy (ZAP) [0] as a blackbox fuzzer for our comparison.

In order to fuzz one endpoint with ZAP, we use its "API Scan" script [1] with an a python wrapper that converts our HTTP request from our configuration file into a definition that ZAP understands.
Further, we replace the "API scan policy" with the "default scan policy" to include non-API related vulnerabilities.

The fuzzed configuration file can be defined using the docker-compose file's environment:

```
environment:
  FUZZER_CONFIG: dvwa/sqli_blind_high
```

First, bring up the `web`server component and the database (if needed). Then go into the parent directory and run `docker-compose up zap --build --force-recreate` to launch the tool.

The report containing the findings will be output in `./data/output/`.

- [0] https://www.zaproxy.org/
- [1] https://www.zaproxy.org/docs/docker/api-scan/