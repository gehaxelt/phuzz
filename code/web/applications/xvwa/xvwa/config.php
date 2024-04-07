<?php
$XVWA_WEBROOT = "";
$host = "db";
$dbname = 'db';
$user = "root";
$pass = "password";
$conn = new mysqli($host,$user,$pass,$dbname);
$conn1 = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
$conn1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>