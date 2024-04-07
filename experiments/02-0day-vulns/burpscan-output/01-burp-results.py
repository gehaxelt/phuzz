#!/usr/bin/env python3

import glob
import json
import xml.etree.ElementTree as ET

IGNORED_ISSUES = {
	'Information': ['Cross-origin resource sharing','Private IP addresses disclosed'],
	'Medium': ['Session token in URL']
}

RESULTS = {
	
}

for xml_path in glob.glob("./*/*.xml"):
	plugin_name, xml_file = xml_path.replace("./", "").split("/")
	api_name = xml_file.replace("wordpress_fuzzer-config.","").replace(".json_report.xml", "")
	root = ET.parse(xml_path).getroot()
	for issue in root.findall('issue'):
		i_name = issue.find('name').text
		i_sev = issue.find('severity').text
		#if i_sev in IGNORED_ISSUES.keys():
		#	if i_name in IGNORED_ISSUES[i_sev]:
		#		continue
		#print(plugin_name, api_name, i_sev, i_name)
		if i_sev not in RESULTS:
			RESULTS[i_sev] = {}
		if i_name not in RESULTS[i_sev]:
			RESULTS[i_sev][i_name] = []

		plugin_api = f"{plugin_name}/{api_name}"
		if plugin_api not in RESULTS[i_sev][i_name]:
			RESULTS[i_sev][i_name].append(plugin_api)

with open("01-burp_results.json","w") as f:
	d = json.dumps(RESULTS, indent=2)
	f.write(d)
	print(d)
