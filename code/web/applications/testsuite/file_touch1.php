<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

touch($orig_filename);
touch($filename);
