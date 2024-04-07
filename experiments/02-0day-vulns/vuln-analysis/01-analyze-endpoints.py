import json


FILE="vulnerable-endpoints.txt"

VULNTYPES={}
ERRORS=set()
VULN_COUNTER={}
ALL_CANDIDATES_COUNTER=0

for f in open(FILE):
	f = f.strip()
	try:
		data = json.load(open(f))
		vulntypes = data.keys()
		for vulntype in vulntypes:
			if not vulntype in VULNTYPES:
				VULNTYPES[vulntype] = set()
				VULN_COUNTER[vulntype] = 0

			VULNTYPES[vulntype].add(f)
			VULN_COUNTER[vulntype] += len(data[vulntype])
			ALL_CANDIDATES_COUNTER += len(data[vulntype])
	except Exception as e:
		error = f"Error: {e},File: {f}"
		print(error)
		ERRORS.add(error)

with open("vulnerable-endpoints-json-errors.txt","w") as f:
	for error in ERRORS:
		f.write(f"{error}\n")

for vulntype in VULNTYPES.keys():
	with open(f"vulnerable-endpoints-{vulntype}.txt","w") as f:
		f.write(f"# {vulntype} / Files: {len(VULNTYPES[vulntype])} / Candidates total: {VULN_COUNTER[vulntype]}\n")
		for vuln in sorted(VULNTYPES[vulntype]):
			f.write(f"{vuln}\n")

print(f"Total candidates: {ALL_CANDIDATES_COUNTER}")