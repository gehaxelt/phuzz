<?php
$db = new PDO("mysql:dbname=db;host=db", "user", "password");
$db->exec("CREATE TABLE IF NOT EXISTS test (id INT, name VARCHAR(20))");
$db->exec("INSERT INTO test (id, name) VALUES (1, 'Sebastian')");
$db->exec("INSERT INTO test (id, name) VALUES (2, 'Lorenz')");

$result1 = $db->exec("SELECT * FROM test"); // should work

$result2 = $db->exec("SELECT * FROM " . $_GET['input']); // sqli