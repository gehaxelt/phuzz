WFuzz
==============================

We use the WFuzz [0] as a blackbox fuzzer for our comparison.

In order to fuzz one endpoint with WFuzz, a python wrapper that converts our HTTP request from our configuration file into commandline arguments that WFuzz understands.
Since WFuzz requires a wordlist, we generate one from its repository by cloning `https://github.com/xmendez/wfuzz` and then running `cat vulns/*| sort -u > wordlist.txt`.

The fuzzed configuration file can be defined using the docker-compose file's environment:

```
environment:
  FUZZER_CONFIG: dvwa/sqli_blind_high
```

First, bring up the `web`server component and the database (if needed). Then go into the parent directory and run `docker-compose up wfuzz --build --force-recreate` to launch the tool.

The report containing the findings will be output in `./data/output/`.

- [0] https://wfuzz.readthedocs.io/en/latest/