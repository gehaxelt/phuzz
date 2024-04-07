We faced many issues running Witcher https://github.com/sefcom/Witcher/ (see below) due to build and dependency issues. Difficulties running Witcher were also observed by others. Unfortunately, issues were still open in Witcher's repository, so we were unable to resolve the issues:


We first followed the instruction in the repository's README to clone it and initiate the submodules.
Running `docker/build-all.sh` eventually fails to compile the custom version of `php5`:

```
The command '/bin/sh -c cd /phpsrc &&                 ./configure               --with-apxs2=/usr/bin/apxs              --enable-cgi                    --enable-ftp                    --enable-mbstring          --with-gd                                        --with-mysql                    --with-pdo-mysql                --with-zlib             && printf "\033[36m[Witcher] PHP $PHP_VER Configure completed \033[0m\n"' returned a non-zero code: 127
Failed to build php5build
```

As we would compare against the more recent version of PHP7, we comment out "php5" in the shell script. The build continues, but eventually fails again for the "witcher/java" container:

```
Step 4/16 : COPY --chown=wc:wc /jdksrc/build/linux-x86_64-normal-server-release/ /jdk-release/
COPY failed: file not found in build context or excluded by .dockerignore: stat jdksrc/build/linux-x86_64-normal-server-release/: file does not exist
Failed to build witcher/java using Dockerfile /root/Witcher2/docker/java.Dockerfile and context /root/Witcher2/docker/../java
```

We assumed that our comparison won't require java, so we remove that component from the shell-script. Finally, it finishes building the containers after a long building phase, but in contrast to the shell script's name, it does not appear to build all required containers.

To build the "openemr" application container, we require the "witcher/phpwebfuzz" container, which is not yet build. We eventually find hints of this container in  "docker/docker-compose.yml", but this file is incorrectly formatted and misses key structural information:

```
ERROR: The Compose file './docker-compose.yml' is invalid because:                                              
Unsupported config option for services: 'php5build'  
```

We manually remediated the issues in the "docker-compose.yml" file, but building the defined "phpwebfuzz" container fails due to a missing "witcher/php7" container, which does not appear to be publicly available.

```
Building phpwebfuzz
Step 1/10 : FROM witcher/php7
ERROR: Service 'phpwebfuzz' failed to build: pull access denied for witcher/php7, repository does not exist or may require 'docker login': denied: requested access to the resource is denied
```

There is no definition or mention of a "witcher/php7" container apparent in the repository, other than the "php7.Dockerfile". Unfortunately this file, which may build such a container, is 0 bytes in size, so critical information is missing. https://github.com/sefcom/Witcher/blob/master/docker/php7.Dockerfile