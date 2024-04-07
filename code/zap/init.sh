#!/bin/bash
set -x

mkdir -p ./data/output/ 

# Override API scanning with Default scanning policy
cp /home/zap/.ZAP/policies/Default\ Policy.policy /home/zap/.ZAP/policies/API-Minimal.policy

# For evaluation with timeouts
#PYTHONUNBUFFERED=1 timeout 300s python3 ./run.py /app/configs/${FUZZER_CONFIG}.json /zap/data/output/
PYTHONUNBUFFERED=1 python3 ./run.py /app/configs/${FUZZER_CONFIG}.json /zap/data/output/
