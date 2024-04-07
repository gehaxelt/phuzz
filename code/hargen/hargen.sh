#!/bin/bash

python ./hargen.py \
    --har-path=/resources/dvwa_sqli_xss.har \
    --out-dir=/configs \
    --set-cookies-include=security \
    --fuzz-body \
    --fuzz-body-include=id \
    --fuzz-query \
    --fuzz-query-include=id \
    --fuzz-headers \
    --set-headers-include=dnt