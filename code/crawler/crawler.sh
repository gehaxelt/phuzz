#!/bin/bash

python3 crawler.py \
--entrypoint http://web \
--harfile ./output.har \
--cookie-path /app/  \
--timeout 3600
