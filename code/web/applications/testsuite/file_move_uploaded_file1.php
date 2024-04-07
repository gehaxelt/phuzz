<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

move_uploaded_file($orig_filename, "/tmp/file");
move_uploaded_file($filename, "/tmp/file");
