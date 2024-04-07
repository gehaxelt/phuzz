
import os
import json

FILEMAP = {
	'opre': 'vulnerable-endpoints-OpenRedirect.txt',
	'pat': 'vulnerable-endpoints-PathTraversal.txt',
	'sqli': 'vulnerable-endpoints-SQLi.txt',
	'xss': 'vulnerable-endpoints-WebFuzzXSSVulnCheck.txt'
}
VULNMAP = {
	'opre': 'OpenRedirect',
	'pat': 'PathTraversal',
	'sqli': 'SQLi',
	'xss': 'WebFuzzXSSVulnCheck'
}

def params2str(d, with_val=True):
	s = ""
	for t in ['headers','cookies','query_params','body_params']:
		if not d[t]:
			continue
		s += f" {t}:"
		# for p,v in d[t].items():
		# 	s += f" {p}"
		# 	if with_val:
		# 		s += f"={v}"
		if with_val:
			s += json.dumps(d[t])
		else:
			s += json.dumps(sorted(list(d[t].keys())))

	return s

for vulncat in FILEMAP.keys():
	with open(FILEMAP[vulncat]) as f:
		param_set = {}
		debug_set = {}

		for line in f:
			line = line.strip()
			if line.startswith("#"):
				continue
			# ../phuzz-output/900000-ninja-forms.3.6.23.zip/connect/output/fuzzer-1/vulnerable-candidates.json
			path_parts = line.split("/")
			
			data = json.load(open(line))

			config_dir = path_parts[1]
			plugin_name = path_parts[2]
			api_name = path_parts[3]
			output = path_parts[4]
			fuzzer_id = path_parts[5]

			fuzzer_dir = os.path.join(path_parts[0],config_dir, plugin_name, api_name, output, fuzzer_id)
			
			the_key = f"{plugin_name}-{api_name}"
			if the_key not in param_set:
				param_set[the_key] = set()
				debug_set[the_key] = set()

			dir_name = f"{vulncat}-analysis"
			f_name = f"{the_key}.txt"
			df_name = f"{the_key}.debug"

			if not os.path.isdir(dir_name):
				os.mkdir(dir_name)

			f_path = os.path.join(dir_name, f_name)
			df_path = os.path.join(dir_name, df_name)

			for candidate in data[VULNMAP[vulncat]]:
				c_id = candidate['coverage_id']
				fixed_params = candidate['fixed_params']
				fuzz_params = candidate['fuzz_params']

				merged_params = {
					'headers': fixed_params['headers'] | fuzz_params['headers'],
					'cookies': fixed_params['cookies'] | fuzz_params['cookies'],
					'query_params': fixed_params['query_params'] | fuzz_params['query_params'],
					'body_params': fixed_params['body_params'] | fuzz_params['body_params']
				}

				debug_file = os.path.join(fuzzer_dir, f"{VULNMAP[vulncat]}-{c_id}.json")
				if os.path.exists(debug_file):
					for dline in open(debug_file):
						debug_set[the_key].add(dline.strip())

				param_keys = params2str(merged_params,with_val=False)
				if param_keys in param_set[the_key]:
					continue

				param_set[the_key].add(param_keys)
				params_str = params2str(merged_params)
				# print(vulncat, plugin_name, api_name)
				# print(params_str)

				with open(f_path, "a") as of:
					of.write(f"{line},{c_id}: {params_str}\n")

			if debug_set[the_key]:
				with open(df_path,"w") as of:
					for dline in sorted(list(debug_set[the_key])):
						of.write(f"{dline}\n")