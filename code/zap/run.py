#!/usr/bin/env python3

import os
import sys
import json
import subprocess
import pprint
from phuzz import Phuzz
from urllib.parse import urlparse, parse_qsl, urlunparse

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
URL = preq.url
url_parts = list(urlparse(URL))
base_url = url_parts[0] + "://" + url_parts[1]
endpoint = url_parts[2]
query_params = dict(parse_qsl(url_parts[4]))
params = []

for h,v in preq.headers.items():
    if h == "Content-Length":
        continue
    params.append({
        "name": h,
        "in": "header",
        "schema": {
            "type": "string",
            "default": v if type(v) == str else ";".join(v)
        }
    })

for q,v in query_params.items():
    params.append({
        "name": q,
        "in": "query",
        "schema": {
            "type": "string",
            "default": v
        }
    })

openapi_json = {
  "openapi": "3.0.3",
  "info": {
    "title": config_name,
    "version": "1.0.11"
  },
  "servers": [
    {
      "url": base_url
    }
  ],
  "paths": {
    endpoint: {
      preq.method.lower(): {
        "parameters": params,
        "responses": {
          "200": {
            "description": "OK"
          }
        }
      }
    }
  }
}


if preq.body:
    bparams = {}
    body_params = dict(parse_qsl(preq.body))
    for b, v in body_params.items():
        bparams[b] = {
            "type": "string",
            "default": v
        }
    openapi_json['paths'][endpoint][preq.method.lower()]['requestBody'] = {
      "content": {
        preq.headers['Content-Type'].lower(): {
          "schema": {
            "type": "object",
            "properties": bparams
          }
        }
      },
      "required": "true"
    }

#print(openapi_json)
pprint.pprint(openapi_json)

with open("/tmp/openapi.json", "w") as f:
    f.write(json.dumps(openapi_json))

cmd = ["zap-api-scan.py", "-t", "/tmp/openapi.json", "-f", "openapi", "-r", f"{output_dir}/result.html"]
#cmd = ["zap-api-scan.py"]
print(cmd)
subprocess.run(cmd)