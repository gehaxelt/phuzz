<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

fnmatch("*", $orig_filename);
fnmatch("*", $filename);
