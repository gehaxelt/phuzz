<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

rename($orig_filename, "foobar.txt");
rename($filename, "foobar.txt");
