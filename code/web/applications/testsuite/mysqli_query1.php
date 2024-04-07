<?php
$db = mysqli_connect("db", "user", "password", "db");
mysqli_query($db, "CREATE TABLE IF NOT EXISTS test (id INT, name VARCHAR(20))");
mysqli_query($db, "INSERT INTO test (id, name) VALUES (1, 'Sebastian')");
mysqli_query($db, "INSERT INTO test (id, name) VALUES (2, 'Lorenz')");

$result1 = mysqli_query($db, "SELECT * FROM test"); // should work

$result2 = mysqli_query($db, "SELECT * FROM " . $_GET['input']); // sqli