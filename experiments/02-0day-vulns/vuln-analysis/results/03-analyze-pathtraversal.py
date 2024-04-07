import json
import sys
import html

anal_1_results_file = "analysis-pathtraversal-1-vulnfuncs.txt"
OUTFILE = "analysis-pathtraversal-2-vulnplugins.txt"

PLUGINS = {}

class ExitCount(object):

	def __init__(self, max_count, cb):
		self.count = 0
		self.max_count = max_count
		self.callback = cb

	def tick(self):
		self.count += 1
		if self.count >= self.max_count:
			self.callback()

def vulnpath2plugin(path):
	splits = path.split("/output/fuzzer")
	plugin_path = splits[0]
	splits = plugin_path.split("/")

	api_call = splits[-1]
	plugin = '/'.join(splits[:-1])

	return plugin, api_call

def exit():
	print(json.dumps(PLUGINS, indent=2))
	sys.exit(0)

exitcnt = ExitCount(max_count=2500, cb=exit)

FALSE_POSITIVES = set([
	'file_exists:/var/www/html/wp-content/themes/twentytwentythree/functions.php'
])

with open(anal_1_results_file) as f:
	for line in f:
		line = line.strip()

		data = json.loads(line)
		vulnpath = data['vulnpath']
		plugin, api = vulnpath2plugin(vulnpath)

		if not plugin in PLUGINS:
			PLUGINS[plugin] = {}
		if not api in PLUGINS[plugin]:
			PLUGINS[plugin][api] = {}

		#print(data)
		pks = []
		for params in data['params']:
			pks += list(params.keys())
		params_key = "|".join(pks)

		for vulnfunc in data['vulnfuncs']:
			func_key = vulnfunc['function']
			for path in vulnfunc['params']:
				if f"{func_key}:{path}" in FALSE_POSITIVES:
					continue
				path_key = html.unescape(path)
				for param in data['params']:
					for value in sorted(param.values(),key=len, reverse=True): # Sort longest to shortest
						path_key = path_key.replace(value, "<ATTACK>")

				vuln_key = f"{func_key},{params_key},{path_key}"

				if not vuln_key in PLUGINS[plugin][api]:
					PLUGINS[plugin][api][vuln_key] = {
						'func': func_key,
						'path': path,
						'params': params,
						'candidates': [],
					}

				PLUGINS[plugin][api][vuln_key]['candidates'].append(data["covid"])

		#exitcnt.tick()

with open(OUTFILE, "w") as f:
	f.write(json.dumps(PLUGINS, indent=2))