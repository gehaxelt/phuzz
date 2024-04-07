Automated Logins
====================

Automated login scripts can be configured to be called by a fuzzer to perform initial HTTP requests / actions, e.g. logging in / obtaining a valid session / configuring the web application / etc.

While these scripts were primarily being used to prepare session cookies for the fuzzers by storing them in `"/shared-tmpfs/cookies_node{os.environ['FUZZER_NODE_ID']}.json"`, these scripts are more or less obselete if the superior method of function hooking is used to override authentication/authorization check functions.