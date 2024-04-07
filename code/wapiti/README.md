Wapiti
==============================

We use the Wapiti [0] as a blackbox fuzzer for our comparison.

In order to fuzz one endpoint with Wapiti, a python wrapper that converts our HTTP request from our configuration file into commandline arguments that Wapiti understands.

The fuzzed configuration file can be defined using the docker-compose file's environment:

```
environment:
  FUZZER_CONFIG: dvwa/sqli_blind_high
```

First, bring up the `web`server component and the database (if needed). Then go into the parent directory and run `docker-compose up wfuzz --build --force-recreate` to launch the tool.

The report containing the findings will be output in `./data/output/`.

- [0] https://github.com/wapiti-scanner/wapiti