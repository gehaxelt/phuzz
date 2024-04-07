Coverage comparison
====================

This data and code generates the figure 3 in the appendix.

We run the different fuzzers against the custom DVWA SQLi 'fuzz' level and analyze the collected coverage. This level requires the fuzzer to find the special `role=admin` value before the SQL injection in the `id` parameter can be exploited.

PHUZZ manages to identify this vulnerability using its coverage-guided approach.

From `web/applications/dvwa/vulnerabilities/sqli/source/fuzz.php`:
```
if (isset($role) && substr($role, 0, 1) == "a" && isset($id)) {
	if (isset($role) && substr($role, 1, 1) == "d" && isset($id)) {
		if (isset($role) && substr($role, 2, 1) == "m" && isset($id)) {
			if (isset($role) && substr($role, 3, 1) == "i" && isset($id)) {
				if (isset($role) && substr($role, 4, 1) == "n" && isset($id)) {
					$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
					$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query) or die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');

					// Get results
					while( $row = mysqli_fetch_assoc($result )) {
						// Get values
						$first = $row["first_name"];
						$last  = $row["last_name"];

						// Feedback for end user
						$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
					}
				}else {
					echo "wrong role";
				}
			}else {
				echo "wrong role";
			}
		}else {
			echo "wrong role";
		}
	}else {
		echo "wrong role";
	}
}else {
	echo "wrong role";
}
```