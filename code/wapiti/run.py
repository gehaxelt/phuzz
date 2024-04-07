#!/usr/bin/env python3

import os
import sys
import subprocess
from phuzz import Phuzz

config_path = sys.argv[1]
output = sys.argv[2]

config_name = config_path.split("/")[-1]
output_dir = f"{output}/{config_name}"
try:
	os.mkdir(output_dir)
except Exception as e:
	print(e)

phuzz = Phuzz(config_path)
phuzz.load_config()
phuzz.load_request_data()

candidates = list(phuzz.generate_initial_candidates())

if len(candidates) > 1:
	raise Exception("Too many candidates!")
candidate = candidates[0]

preq = phuzz.prepare_request(candidate)
args = []
for h,v in preq.headers.items():
	if h == "Content-Length":
		continue
	args.append("--header")
	args.append(f"{h}: {v}")

if preq.body:
	args.append("--data")
	args.append(preq.body)

URL = preq.url

cmd = ["wapiti","--flush-session", "-f", "html", "-dr", "-o", f"{output_dir}/", "--log", f"{output_dir}/output.log", "-u", URL]
cmd += args
print(cmd)
subprocess.run(cmd)