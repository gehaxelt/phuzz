<?php

$orig_dir = "./testdir/";
$orig_file = $orig_dir . "testfile.txt";

$filename = $orig_file . $_GET['input'];
$directory = dirname($filename);

disk_total_space($orig_dir);
disk_total_space($directory);
