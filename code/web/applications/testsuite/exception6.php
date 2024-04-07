<?php

try {
	$db = mysqli_connect("db", "user", "password", "db");
	mysqli_query($db, "Foobar");
} catch (Throwable $e) {
	echo "Inside catch: " . $e->getMessage();
}