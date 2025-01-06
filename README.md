PHUZZ
=====================================

PHUZZ is a grey-box coverage-guided fuzzer for PHP web applications developed by TBA to be published at AsiaCCS 2024 [0]. 

## Features

- Support for a variety of vulnerabilities: SQL Injection, Command Injection, Insecure Deserialization, Path Traversal, External Entity Injection, Cross-Site Scripting and Open Redirection
- Support for transparent instrumentation without modifying the target's source code, the PHP interpreter's source code or other external components (databases).
- Support for overriding specific functions in the target, e.g. to circument authentication/authorization or other security checks, to gain higher coverage.
- Support for capturing exceptions and errors otherwise hidden by the target, e.g. to find bugs or potential vulnerabilities.
- Support for multi-instance parallel fuzzing
- Support for PHP 8 and 7
- Limited support for independent actions leading to 2nd-order vulnerabilities (e.g. setting a value at endpoint A and triggering a vulnerability at endpoint B)
- Finds more server-side vulnerabilities than state-of-the-art blackbox fuzzer.

## Abstract 

> Coverage-guided fuzz testing has received significant attention from the research community, with a strong focus on binary applications, greatly disregarding other targets, such as web applications. The importance of the World Wide Web in everyone's life cannot be overstated, and to this day, many web applications are developed in PHP.  In this work, we address the challenges of applying coverage-guided fuzzing to PHP web applications and introduce PHUZZ, a modular fuzzing framework for PHP web applications. PHUZZ uses novel approaches to detect more client-side and server-side vulnerability classes than state-of-the-art related work, including SQL injections, remote command injections, insecure deserialization, path traversal, external entity injection, cross-site scripting, and open redirection. We evaluate PHUZZ on a diverse set of artificial and real-world web applications with known and unknown vulnerabilities, and compare it against a variety of state-of-the-art fuzzers. In order to show PHUZZ' effectiveness, we fuzz over 1,000 API endpoints of the 115 most popular WordPress plugins, resulting in over 20 security issues and 2 new CVE-IDs. Finally, we make the framework publicly available to motivate and encourage further research on web application fuzz testing.

If you use PHUZZ, please cite it as:

```
@inproceedings{10.1145/3634737.3661137,
author = {Neef, Sebastian and Kleissner, Lorenz and Seifert, Jean-Pierre},
title = {What All the PHUZZ Is About: A Coverage-guided Fuzzer for Finding Vulnerabilities in PHP Web Applications},
year = {2024},
isbn = {9798400704826},
publisher = {Association for Computing Machinery},
address = {New York, NY, USA},
url = {https://doi.org/10.1145/3634737.3661137},
doi = {10.1145/3634737.3661137},
booktitle = {Proceedings of the 19th ACM Asia Conference on Computer and Communications Security},
pages = {1523–1538},
numpages = {16},
keywords = {PHUZZ, coverage-guided fuzzing, greybox fuzzing, fuzz testing, PHP, vulnerability discovery, web security, SQL injection, remote command execution, cross-site scripting},
location = {Singapore, Singapore},
series = {ASIA CCS '24}
}
```

![PHUZZ overview](./doc/phuzz-overview.png)


## Structure of this repo

This repository contains the code to run PHUZZ and other blackbox fuzzers in headless Docker containers. To learn how to use PHUZZ, please check the README in `./code/`.

The results of our experiments are available in `./experiments/`.

Please also check the subfolders as many have a README with additional information.

## Quick run

```
# Clone the repository
git clone https://github.com/gehaxelt/phuzz.git
cd phuzz/code/

sudo docker-compose up -d db --build --force-recreate
sleep 15s # give the DB some time to start up - might be shorter or longer depending on your hardware. When in doubt, check with docker-compose logs -f db.
sudo docker-compose up -d web --build --force-recreate
sleep 15s # give the DB some time to start up - might be shorter or longer depending on your hardware. When in doubt, check with docker-compose logs -f db.

sudo docker-compose up fuzzer-dvwa-sqli-low-1 --build --force-recreate
# Let the fuzzer run for a while and terminate it with ctrl+c
# View the results with
less fuzzer/output/fuzzer-1/vulnerable-candidates.json
```

## Contributions & Future Work

We acknowledge that PHUZZ is research-grade code, although we tried to keep it as usable for future work as possible. For that, PHUZZ is divided into several modules/components using object oriented programming. Thus, incremental improvements to different aspects can be made, e.g. mutation, candidate selection, etc.

If you have any issues running PHUZZ, encounter bugs or would like to contribute to PHUZZ, please feel free to open issues or propose pull requests!

As for future work, the following features would be great to see in PHUZZ:

- Support for more vulnerability classes (e.g. SSRF, header injection, ...)
- Support for hooking PHP expressions (e.g. eval / include / require / etc.), which is a limitation of UOPZ.
- Algorithm improvements for mutations, scoring, candidate selection, etc.
- Performance improvements (e.g. Python processing, Network communication, etc.) for more requests/second
- Crawler improvements for a more sophisticated interaction with the web application during the crawling process.
- Better support for dependent (stateful) actions, e.g. as performed by Atropos using snapshotting.

Again, feel free to reach out to us if you would like to have a quick chat on how to improve PHUZZ.

## CSAW'24 Applied Research Competition Poster
This work has won the CSAW'24 Applied Research Competition. 

![PHUZZ poster](./doc/poster/csaw-poster.png)

## References

- [0] https://dl.acm.org/doi/10.1145/3634737.3661137
