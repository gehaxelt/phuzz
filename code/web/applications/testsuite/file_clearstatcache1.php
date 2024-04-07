<?php

$orig_dir = "./testdir/";
$orig_file = $orig_dir . "testfile.txt";

$filename = $orig_file . $_GET['input'];
$directory = dirname($filename);

clearstatcache(true, $orig_file);
clearstatcache(true, $filename);
