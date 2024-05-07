#!/bin/bash

python3 crawler.py \
--baseurl http://web \
--entrypoint http://web \
--harfile ./output.har \
--cookie-path /app/  \
--timeout 3600
