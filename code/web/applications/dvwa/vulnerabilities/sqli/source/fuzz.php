<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
	// Get input
	$id = $_REQUEST[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Check database
			$role = $_GET['role'];

/*
			if (substr($id, 0, 1) == "2") {
				echo "foo";
			}
			if (substr($id, 0, 1) == "3") {
				echo "bar";
			}
			if (substr($id, 0, 1) == "4") {
				echo "asd";
			}
*/

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

			mysqli_close($GLOBALS["___mysqli_ston"]);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			#$sqlite_db_connection = new SQLite3($_DVWA['SQLITE_DB']);
			#$sqlite_db_connection->enableExceptions(true);

			$query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
			#print $query;
			try {
				$results = $sqlite_db_connection->query($query);
			} catch (Exception $e) {
				echo 'Caught exception: ' . $e->getMessage();
				exit();
			}

			if ($results) {
				while ($row = $results->fetchArray()) {
					// Get values
					$first = $row["first_name"];
					$last  = $row["last_name"];

					// Feedback for end user
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
			} else {
				echo "Error in fetch ".$sqlite_db->lastErrorMsg();
			}
			break;
	} 
}

?>