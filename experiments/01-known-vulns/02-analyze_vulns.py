import json
import io
import csv
from queue import PriorityQueue
from datetime import datetime
from collections import Counter

with open("01-vulns.json") as f:
	vuln_data = json.load(f)

pq = PriorityQueue()
years = ["2022", "2021", "2020", "2019"]

counter = 0
for vuln in vuln_data:
	if vuln['date'][:4] not in years:
		continue
	counter += 1
	d = datetime.fromisoformat(vuln['date'])
	qo = (-int(d.timestamp()), counter, vuln)
	pq.put(qo)


keywords = {
	'XSS': ['Reflected Cross-Site Scripting', 'Reflected Cross Site Scripting','Reflected XSS', 'reflected cross-site-scripting', 'cross-site scripting', 'cross site scripting', 'xss', 'cross-ste scripting'],
	'SQLi': ['SQL Injection', 'SQLi'],
	'Deserialization': ['Deserialization', 'Object injection', 'objection injection', 'deserialisation'],
	'Path traversal': ['Arbitrary File', 'Local File', 'Remote File', ' LFI', 'file upload', 'path traversal', 'file renaming', 'file removal'],
	'RCE': ['Command Injection', 'Remote Code Execution', 'rce', 'code execution', 'remote command execution', 'code injection'],
	'OpRe': ['open redirect','url handling'],
	'XXE': ['xml', 'XML', 'xxe', 'external entity']
}
categories = {}
for keyword in keywords.keys():
	categories[keyword] = []

skip_words = ['Stored XSS','Stored Cross-Site Scripting', 'Unauthorised AJAX Calls via Freemius',
'Arbitrary Plugin Installation', 'Email Address Disclosure', 'Email Addresses Disclosure', 'Denial of Service', 'Race Condition',
'Arbitrary Plugin Activation','Information Disclosure', 'arbitrary option update', 'Arbitrary Options Update', 'Arbitrary Settings Update',
'via csrf', 'multiple csrf', 'csv injection', 'settings update', 'stored cross site scripting', 'authentication bypass', 'ssrf', 'ajax calls',
'csrf', 'folders disclosure via outdated jqueryfiletree library', 'iframe injection', 'cross-site request forgery', 'bypass',
'information access', 'disclosure','privilege escalation', 'template activation', 'template import', 'theme activation',
'import deletion', 'plugin deactivation', 'post deletion', 'content update', 'Arbitrary email sending', 'template', 'event deletion',
'log access', 'imported csv', 'configuration leak', 'post creation', 'privileges escalation', ' idor',
'input validation', 'user deletion', 'popup deletion', 'broken access control', 'spoofing', 'arbitrary options', 'information exposure',
'server side request forgery', 'storage of password', 'rest calls', 'e-mail sending', 'logs publicly', 'settings reset', 'tabnabbing',
'icon deletion', 'post metadata access', 'image renaming', 'ids activation', 'tab nabbing', 'uri deletion', 'post meta deletion',
'status update', 'tickets download', 'entries export', 'backup download', 'stats reset', 'takeover', 'plugin deletion', 'country ban',
'ticket deletion', 'prototype pollution', 'data download','arbitrary submissions listing', 'layout disabling', 'Unauthorised actions',
'message read/edition', 'app disabling', 'cache deletion', 'network creation', 'Unauthenticated actions', 'votes tampering',
'Arbitrary events', 'css injection', 'Unauthorised calls', 'settings change','css appending', 'links creation', 'alert creation',
'post access', 'log cleanup', 'functionality abuse', 'shipping method creation', 'widget creation', 'views manipulation',
'registration as admin', 'sensitive data exposure', 'cache poisoning', 'side-channel attack', 'smtp credentials', 'setting update',
'Arbitrary function call', 'brand creation', 'option deletion', 'option update', 'abuse of functionality', 'stored cross-site',
'missing authorisation', 'automation creation']

wpvulncategories = Counter()

while pq.qsize():
	prio, cnter, vuln = pq.get()
	title = vuln['title']
	date = vuln['date']
	vuln_desc = vuln['vuln']
	lower_vuln_desc = ''.join(vuln_desc.lower().split(" - ")[1:])

	if lower_vuln_desc == '':
		lower_vuln_desc = ''.join(vuln_desc.lower().split("- ")[1:])

	if lower_vuln_desc == '':
		print("Skipping: ", vuln_desc)


	found_category = False
	skip = False
	for vuln_type in keywords.keys():
		for keyword in keywords[vuln_type]:
			if keyword.lower() in lower_vuln_desc:
				#print("keyword: ", keyword)
				found_category = vuln_type
				break
		if found_category:
			break

	for skip_word in skip_words:
		if skip_word.lower() in lower_vuln_desc:
			skip = True
			break

	if not found_category:
		if not skip:
			try:
				wpvulncategories.update([lower_vuln_desc])
			except Exception as e:
				print(lower_vuln_desc)
	else:
		if not skip:
			categories[found_category].append(vuln)
		#print(date, vuln_desc)

print("Not classified: ", wpvulncategories)

sio = io.StringIO()
sio_csv = io.StringIO()

csvwriter = csv.writer(sio_csv, delimiter=",")
csvwriter.writerow(["Category", "Date", "Vuln", "Link"])

for category in categories:
	s = "\n#### Category: " + str(category) + " " + str(len(categories[category]))
	sio.write(s + "\n")
	#print(s)
	for vuln in categories[category]:
		s = vuln['date'] + " " + vuln['vuln'] + " " + "https://wpscan.com/" + vuln['href']
		sio.write(s + "\n")
		#print(s)
		csvwriter.writerow([category, vuln['date'], vuln['vuln'],"https://wpscan.com/" + vuln['href']])

with open("02-analyzed_vulns.txt", "w") as f:
	f.write(sio.getvalue())

with open("02-analyzed_vulns.csv", "w") as f:
	f.write(sio_csv.getvalue())

print("Analyzed: ", counter)