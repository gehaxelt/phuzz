<?php

$orig_dir = "./testdir/";
$orig_file = $orig_dir . "testfile.txt";

$filename = $orig_file . $_GET['input'];
$directory = dirname($filename);

copy($orig_file, "/tmp/copy");
copy($filename, "/tmp/copy");
