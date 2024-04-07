<?php
$db = new PDO("mysql:dbname=db;host=db", "user", "password");
$db->query("CREATE TABLE IF NOT EXISTS test (id INT, name VARCHAR(20))");
$db->query("INSERT INTO test (id, name) VALUES (1, 'Sebastian')");
$db->query("INSERT INTO test (id, name) VALUES (2, 'Lorenz')");

$result1 = $db->query("SELECT * FROM test"); // should work

$result2 = $db->query("SELECT * FROM " . $_GET['input']); // sqli