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
	if not (h == "Cookie" and v == "security=fuzz"): # DVWA sqli fuzz level workaround!
		v = v.replace("fuzz", "FUZZ")
	args.append("-H")
	args.append(f"{h}:{v}")

if preq.body:
	args.append("-d")
	args.append(preq.body.replace("fuzz", "FUZZ"))

args.append("-X")
args.append(preq.method)

URL = preq.url.replace("fuzz", "FUZZ")

cmd = ["wfuzz", "-A", "-Z", "-f", f"{output_dir}/result.html,html", "-w", "./wordlist.txt", "--no-cache", "-u", URL]
cmd += args
print(cmd)
subprocess.run(cmd)