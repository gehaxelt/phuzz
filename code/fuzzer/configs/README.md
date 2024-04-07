Configuration files
=====================

All endpoints to be fuzzed by PHUZZ or other tools have to have individual configuration files.

The configuration file specifies the HTTP endpoint, HTTP request method and all parameters that should be sent to the targeted web application.
For each parameter class (headers, cookies, query params, body params), one can define which parameters should be fuzzed and which one should not be changed.
This allows for great flexibility when the web application expects certain values to be set, e.g. `submit=submit`.

```
"target": "http://web/wp-content/plugins/crm-perks-forms/templates/sample_file.php",
```
`target` specifies the URL of the fuzzed endpoint. It is crucial that the `host` part matches the internal docker network, e.g. the `http://web/` container.

```
"login": "dvwa_requests",
```

This specifies the login script from `automated_logins/` to be loaded. If no login script is used, this key can be omitted.

```
"methods": [
	"GET"
],
```

Specify which HTTP methods should be used for the requests. If multiple methods are defined, PHUZZ will generate N different initial HTTP requests.

The keys `headers`, `cookies`, `query_params` and `body_params` are dictionaries holding the following subvalues for the respective parameter class:

```
"data": [
	{
		"name": "param1",
		"value": "value1"
	},            
	{
		"name": "target",
		"seeds": [
		    "fuzz"
		]
	}
]
```

`data` specifies the parameters in the parameter class. The values can either be single one (`value1`) or multiple initial values (`seeds`). When multiple seed-values are specified, PHUZZ will generate N initial requests with each value.

```
"fixed": [
  "submit"
],
"fuzz": [
	"*"
],
"weight": 1
```
These keys can be used to define parameter names that should not be changed (`fixed`) or should be considered for fuzzing mutations (`fuzz`). By default, all parameters will be fuzzed if `fuzz` is an empty list. 
The key `weight` can be used to change the relative priority of the parameter class for the mutations.
