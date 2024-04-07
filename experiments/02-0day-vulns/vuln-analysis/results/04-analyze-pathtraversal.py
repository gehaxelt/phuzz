import json
import sys

vulnpluginsfile = "analysis-pathtraversal-2-vulnplugins.txt"
OUTFILE = "analysis-pathtraversal-3-vulnplugins-funcs.txt"
VULNPLUGINS = {
	
}

with open(vulnpluginsfile) as f:
	data = json.load(f)

	for plugin in data.keys():
		for api in data[plugin].keys():
			if not data[plugin][api]:
				continue

			for vk in data[plugin][api].keys():
				vulnfunc = data[plugin][api][vk]["func"]
				vulnpath = data[plugin][api][vk]["path"]
				vulnparams = data[plugin][api][vk]["params"]

				if not vulnfunc in VULNPLUGINS:
					VULNPLUGINS[vulnfunc] = {
						'equal': set(),
						'prefix': set(),
						'suffix': set(),
						'unknown':set()
					}
				pkey = f"{plugin},{api}"

				for param in vulnparams.keys():
					for value in vulnparams[param]:
						if not value in vulnpath:
							continue
						if vulnpath.startswith(value) and vulnpath.endswith(value):
							VULNPLUGINS[vulnfunc]['equal'].add(pkey)
						elif vulnpath.startswith(value):
							VULNPLUGINS[vulnfunc]['prefix'].add(pkey)
						elif vulnpath.endswith(value):
							VULNPLUGINS[vulnfunc]['suffix'].add(pkey)
						else:
							VULNPLUGINS[vulnfunc]['unknown'].add(pkey)

with open(OUTFILE, "w") as f:
	f.write(json.dumps(VULNPLUGINS, indent=2, default=list))

