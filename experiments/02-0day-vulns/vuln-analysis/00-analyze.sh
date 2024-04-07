#!/bin/bash
set -x

find ../phuzz-output -type f -name '*vulnerable*.json' | sort -u > vulnerable-endpoints.txt


## General info
#wc -l vulnerable-endpoints.txt 
# 691 vulnerable-endpoints.txt

#cat vulnerable-endpoints.txt | cut -d'/' -f1-5 | sort -u | wc -l
# 142

#cat vulnerable-endpoints.txt | cut -d'/' -f1-3 | sort -u | wc -l
# 54

python3 01-analyze-endpoints.py

## Open redirection
# cat vulnerable-endpoints-OpenRedirect.txt | grep 'configs' | cut -d'/' -f1-4 | sort -u | wc -l
# 1 endpoints
# cat vulnerable-endpoints-OpenRedirect.txt | grep 'configs' | cut -d'/' -f1-3 | sort -u | wc -l
# 1 plugins
# cat vulnerable-endpoints-OpenRedirect.txt | xargs -I '{}'  jq -r '.[][].coverage_id' "{}" | sort -u | wc -l 
# 1026 candidates

## PathTraversal
#cat vulnerable-endpoints-PathTraversal.txt | grep 'configs' | cut -d'/' -f1-4 | sort -u | wc -l
# 110 endpoints
#cat vulnerable-endpoints-PathTraversal.txt | grep 'configs' | cut -d'/' -f1-3 | sort -u | wc -l
# 47 plugins
#cat vulnerable-endpoints-PathTraversal.txt | xargs -I '{}'  jq -r '.[][].coverage_id' "{}" | sort -u | wc -l 
# 60020 candidates

## SQLi
#cat vulnerable-endpoints-SQLi.txt | grep 'configs' | cut -d'/' -f1-4 | sort -u | wc -l
# 9 endpoints
#cat vulnerable-endpoints-SQLi.txt | grep 'configs' | cut -d'/' -f1-3 | sort -u | wc -l
# 6 plugins
#cat vulnerable-endpoints-SQLi.txt | xargs -I '{}'  jq -r '.[][].coverage_id' "{}" | sort -u | wc -l 
# 8168 candidates

## XSS
#cat vulnerable-endpoints-WebFuzzXSSVulnCheck.txt | grep 'configs' | cut -d'/' -f1-4 | sort -u | wc -l
# 24 endpoints
#cat vulnerable-endpoints-WebFuzzXSSVulnCheck.txt | grep 'configs' | cut -d'/' -f1-3 | sort -u | wc -l
# 14 plugins
#cat vulnerable-endpoints-WebFuzzXSSVulnCheck.txt | xargs -I '{}'  jq -r '.[][].coverage_id' "{}" | sort -u | wc -l 
# 1187 candidates

## Get server-side debug info for SQLi and PathTraversal. There's no such info for open redirection and XSS, since these are detected on the client-side.
find ../phuzz-output -type f -name 'SQLi-*.json' > vulnerable-candidates-debuginfo-SQLi.txt
find ../phuzz-output -type f -name 'PathTraversal-*.json' > vulnerable-candidates-debuginfo-PathTraversal.txt

python3 02-analyze-vulns.py