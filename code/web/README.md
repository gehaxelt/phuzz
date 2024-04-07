Web component
==================

This component contains the web server, the server-side instrumentation and web applications that should be fuzzed.

By default, the container is built using the PHP version 8, but a PHP 7 version also exists. If you need PHP 7, change `dockerfile: Dockerfile` to `dockerfile: Dockerfile.php7` in the docker-compose.yml.

## Applications

If you want to add a fuzz target, create a new folder in the `./applications/` directory and place the files that should be copied to the containers `/var/www/html/` (DocumentRoot) in there.
Furthermore, you can create a folder named `_overrides` in the application's folder. The instrumentation will load and execute any `*.php` file that you place there, e.g. to load application-specific instrumentation/function hooks.
Additionally, you can place an `init.sh` shell script in the application's folder that will be executed upon the container's startup, e.g. to change the web server's configuration.

The following applications are supported by default:

- bWAPP - https://github.com/lmoroz/bWAPP
- dvwa -  https://github.com/digininja/DVWA
- testsuite - A collection of test files for specific functions or vulnerabilities.
- wackopicko - https://github.com/adamdoupe/WackoPicko adapted to be compatible with PHP 8
- wordpress - https://wordpress.org/ with a set of 22 plugins with known vulnerabilities.
- xvwa - https://github.com/s4n7h0/xvwa

## Configs

This folder contains the Apache webserver's configuration file (`mpm_prefork.conf`) and PHP configuration file (`php.ini`). The latter configures the required PHP extensions for function hooking and coverage collection.

PHUZZ supports coverage collection with PCOV and XDebug. PCOV is said to be more performant, so if you prefer to use Xdebug, you will have to set `pcov.enable=0`.

Also, Opcache is used to increase the PHP performance. If you use PHUZZ to debug an application and want to change the PHP files, set `opcache.validate_timestamps=1`.

## Instrumentation

This folder contains the PHP files that will be copied into the docker container to perform the target instrumentation. 

`__fuzzer_startcov.php` is loaded by `auto_prepend_file` in the PHP configuration, and `__fuzzer_stopcov.php` executed by `auto_append_file`. The former initializes the coverage collection and loads the function hook definitions which are in separate files in `overrides.d/`.

The instrumentation is done for all 

