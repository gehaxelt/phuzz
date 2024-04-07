#!/bin/bash
mkdir analysis configs || true
find plugins/ -type f -iname '*.zip' | while read fpath; do echo "Checking $fpath"; python3 ./01-analyze-plugin.py -p "$fpath"; done | tee plugin-analysis.log
