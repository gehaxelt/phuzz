import json
import sys

VPATHFILE = "../vulnerable-candidates-debuginfo-PathTraversal.txt"
VULNPATHS = []
with open(VPATHFILE) as f:
	for l in f:
		VULNPATHS.append(l.strip())

def find_vulnpathfile(covid):
	for p in VULNPATHS:
		if covid in p:
			return p
	return None

VCFILE = "../vulnerable-endpoints-PathTraversal.txt"
OUTFILE = "analysis-pathtraversal-1-vulnfuncs.txt"

# empty file
with open(OUTFILE, "w") as f:
	pass

with open(VCFILE) as f:
	for line in f:
		MATCHES = []
		try:
			line = "../" + line.strip()
			candidates = json.load(open(line))
			print(line)
		except Exception as e:
			print("failed to open file: ", line)
			continue
		for candidate in candidates['PathTraversal']:
			#print(candidate)

			covid = candidate["coverage_id"]
			fuzz_params = []
			for k in candidate["fuzz_params"].keys():
				if candidate["fuzz_params"][k].values():
					fuzz_params.append(dict(candidate["fuzz_params"][k].items()))

			vulnpath = find_vulnpathfile(covid)
			if vulnpath is None:
				print("Failed to find vulnpath file: ", covid)
				continue

			vulnfuncs = []
			with open("../" + vulnpath) as vpf:
				for l in vpf:
					l = l.strip()
					data = json.loads(l)
					params_str = "|||".join(data['params'])
					#print(fuzz_params)
					for fp in fuzz_params:
						for k,v in fp.items():
							if v in params_str:
								vulnfuncs.append(data)

					# if any(map(lambda x: x in , fuzz_params.values())):
					# 	vulnfuncs.append(l)

			MATCHES.append({
				'covid': covid,
				'vulnpath': vulnpath,
				'params': fuzz_params,
				'vulnfuncs': vulnfuncs,
				})

			#print(covid, fuzz_params, vulnpath, vulnfuncs)

		with open(OUTFILE, "a") as f:
			for match in MATCHES:
				f.write(json.dumps(match))
				f.write("\n")