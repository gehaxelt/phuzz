<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

link("/tmp/link1", $orig_filename, 0);
link("/tmp/link2", $filename, 0);
