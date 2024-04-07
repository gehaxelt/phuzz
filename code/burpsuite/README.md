Burpsuite
====================

We instrument BurpSuite Professional [0] using a modified Carbonator [1] to run the blackbox scanner within a headless docker-container.

# Setup

First, obtain a license for BurpSuite Professional from [0]. Download the BurpSuite Application as a JAR file and place it in this folder named `burpsuite-pro.jar`.

Run `java -jar burpsuite-pro.jar` on the host and activate the the tool with the license. After successful activation, you copy the contents of `~/.java/` into `./.java/`. Now BurpSuite Professional will be activated within the docker container.  

Ensure that the folder `./data/output/` exists and is world-writable to avoid permission issues.

The `Lib.zip` archive contains the jython2.7 environment required to run the carbonator extension. Once BurpSuite supports jython3, the installation of the jython environment might be programmatically done within the `Dockerfile`.

Finally run `docker-compose build burpsuite` and `docker-compose run burpsuite` from the **parent** directory to check if the activation and carbonator instrumentation works. 

# Usage

The endpoint to be fuzzed by BurpSuite is defined by the configuration files in `fuzzer/configs/`. You can define the configuration file to be used with the environment variables in the parent directory's `docker-compose.yml`:
```
environment:
  FUZZER_CONFIG: dvwa/sqli_blind_high
```
By default, the generated HTML report will be saved in `./data/output/<config-name>.json_report.html`.
**Important:** The names of the config files are without the `.json` suffix.

Depending on the beefiness of your hardware, you might want to adjust the timeout after which carbonator will stop BurpSuite and generate the report with the findings. Change `self.scan_timeout = 300` in `carbonator.py` to a suitable timeout in seconds.

Finally, go into the parent directory and run `docker-compose up burpsuite --build --force-recreate` to launch the tool.

The output should look similar to this:

```
Attaching to burpsuite-1
burpsuite-1  | + mkdir ./data/output
burpsuite-1  | mkdir: cannot create directory './data/output': File exists
burpsuite-1  | + /bin/java --add-opens java.base/java.lang=ALL-UNNAMED --add-opens java.base/javax.crypto=ALL-UNNAMED --add-opens java.desktop/javax.swing=ALL-UNNAMED --illegal-access=permit -Xmx1024m -Djava.awt.headless=true -jar ./burpsuite-pro.jar --data-dir=./data/ --user-config-file=./user.json /app/configs/dvwa/sqli_low.json
burpsuite-1  | OpenJDK 64-Bit Server VM warning: Ignoring option --illegal-access=permit; support was removed in 17.0
burpsuite-1  | Your JRE appears to be version 17.0.10 from Private Build
burpsuite-1  | Burp has not been fully tested on this platform and you may experience problems.
burpsuite-1  | array(java.lang.String, [u'/app/configs/dvwa/sqli_low.json'])
burpsuite-1  | Initiating Carbonator Against:  http://web/vulnerabilities/sqli/
burpsuite-1  | ('Fixed headers', {})
burpsuite-1  | ('Fixed Cookies', {u'security': [u'low']})
burpsuite-1  | ('Fixed Query Params', {u'Submit': [u'Submit']})
burpsuite-1  | ('Fixed Body Params', {})
burpsuite-1  | ('Fuzz headers', {})
burpsuite-1  | ('Fuzz cookies', {})
burpsuite-1  | ('Fuzz query params', {u'id': [u'fuzz']})
burpsuite-1  | ('Fuzz body params', {})
burpsuite-1  | ('Using candidate: ', "{'fuzz_params': {'headers': {}, 'body_params': {}, 'cookies': {}, 'query_params': {u'id': u'fuzz'}}, 'fixed_params': {'headers': {}, 'body_params': {}, 'cookies': {u'security': u'low'}, 'query_params': {u'Submit': u'Submit'}}, 'http_target': u'http://web/vulnerabilities/sqli/', 'http_method': u'GET', 'fuzz_weights': {'headers': 0.25, 'body_params': 0.25, 'cookies': 0.0, 'query_params': 1.0}}")
burpsuite-1  | ('Queueing request:', u'GET /vulnerabilities/sqli/?Submit=Submit&id=fuzz HTTP/1.0\nHost: web\nCookie: security=low\n\n')
burpsuite-1  | Waiting...300
burpsuite-1  | Waiting...299
burpsuite-1  | Waiting...298
[...]
burpsuite-1  | Waiting...0
burpsuite-1  | Removing Listeners
burpsuite-1  | Generating reports
burpsuite-1  | Deleting temporary files - please wait ... done.
burpsuite-1 exited with code 0
```

Now, you should find the report containing the findings in the `./data/output/` directory:

```
$ ls burpsuite/data/output/*.*
-rw-r--r-- 1 999 adm 78K Apr  7 13:24 burpsuite/data/output/dvwa_sqli_low.json_report.html
-rw-r--r-- 1 999 adm 46K Apr  7 13:24 burpsuite/data/output/dvwa_sqli_low.json_report.xml
```

- [0] https://portswigger.net/burp/pro
- [1] https://github.com/umarfarook882/carbonator/