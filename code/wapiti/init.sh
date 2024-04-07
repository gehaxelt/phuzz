#!/bin/bash
set -x

mkdir ./data/output/ 

# for evaluation with timeout
#PYTHONUNBUFFERED=1 timeout 300s python3 ./run.py /app/configs/${FUZZER_CONFIG}.json ./data/output/
PYTHONUNBUFFERED=1 python3 ./run.py /app/configs/${FUZZER_CONFIG}.json ./data/output/
