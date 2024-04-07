<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

file_put_contents($orig_filename, "");
file_put_contents($filename, "");
