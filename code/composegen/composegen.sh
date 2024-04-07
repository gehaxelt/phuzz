#!/bin/bash

python ./composegen.py \
    --output-dir /app \
    --configs "dvwa/sqli_low:8" \
    --application-type dvwa

# python ./composegen.py \
#     --output-dir /app \
#     --config-dir ../fuzzer/configs \
#     --num-instances 2 \
#     --application-type dvwa

chmod 777 /app/docker-compose.yml