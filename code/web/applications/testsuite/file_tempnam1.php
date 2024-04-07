<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

tempnam($orig_directory, "prefix");
tempnam($directory, "prefix" . $_GET['input']);
