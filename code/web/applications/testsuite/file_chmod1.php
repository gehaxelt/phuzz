<?php

$orig_dir = "./testdir/";
$orig_file = $orig_dir . "origfile.txt";

$filename = $orig_file . $_GET['input'];
$directory = dirname($filename);

chmod($orig_file, 0);
chmod($filename, 0);
