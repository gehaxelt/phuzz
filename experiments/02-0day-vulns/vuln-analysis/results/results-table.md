Vuln class | candidates | APIs (plugins) | Valid
--------------------------------------------------------------------------------------------
Opre	| 1,026 | 1 (1)   |  1 
SQLi	| 8,168 | 9 (6)   |  0 
XSS		| 1,187 | 24 (14) |  7 
PaTr	| 60,020| 110 (47)| 16 

=> 137 unique endpoints

What is a vulnerability?
- A vulnerable function that can successfully be exploited by a non-administrator and without the knowledge of secret tokens.
- In multi-site Wordpress installations this is different, as 'administrator' is role that subsite-users can get. In such cases, they can have valid secret tokens or nonces.

# OpRe
- input passed to `wp_redirect`, but requires a nonce to successfully exploit.

# SQLi
- No vulnerability, but several programming bugs: Deadlocks, failed queries due to missing ORDER statement parameters, or missing database tables.
- The latter might be caused by inter-plugin depdencies or missing configuration or use of specific functionality.

# XSS
- The vulndetection of webFuzz appears to ignore the conten-type, thus reporting false positives for HTTP responses with a "application/json" content-type.
- In other cases sufficient encoding of the payload happens, which prevents a successful attack, but still preseves the 0xdeadbeef marker.
- Several cases have the potential to become XSS issues, but are not exploitable without knowing secret nonces or setting a correct referrer-header, which is not feasible.

# PaTr
- Many false positives due to "fu" matching the theme's "functions.php" and similar cases.
- Nonetheless, it successfully identifies cases in which fuzz-input reaches file-related functions, i.e. file_exists, file, fopen, unlink, is_dir, is_readable, mkdir, realpath
- Some cases appear to be in Wordpress-provided functionality, such as `activate_plugin` or `activate_plugins`, but also the Plugin_Upgrader. The former sanitizes the path, the latter does not.
