<?php

$username = "wackopicko";
$pass = "webvuln!@#";
$database = "wackopicko";

require_once("database.php");
$db = new DB("db", $username, $pass, $database);

define("OURDB", $db);

?>