<?php
$user_input = $_GET['input'];

$result = unserialize("a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}");
echo var_dump($result);

$result = unserialize($user_input);
echo var_dump($result);
