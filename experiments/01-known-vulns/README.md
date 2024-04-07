Fuzzing of Known Vulns
===========================

In this experiment, we compare PHUZZ against other vulnerability scanners (BurpSuite Pro, ZAP, WackoPicko, WFuzz) with a diverse set of web applications with known vulnerabilities.

We use the following web applications:

- bWAPP provides 30 vulnerabilities
- DVWA provides 18 vulnerabilities
- XVWA provides 10 vulnerabilities
- WackoPickko provides 7 vulnerabilities
- WordPress with 22 vulnerable plugins

For WordPress, we use the following 22 plugins, which we find using `01-scrape-wpvulndb.py` and `02-analyze_vulns.py`.

![WP Plugin Vulnerabilities](../../doc/known-wp-plugin-vulns.png)

See the `known-vulns.csv` for the results.