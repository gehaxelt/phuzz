<?php

$orig_directory = "./testdir/";
$orig_filename = $orig_directory . "testfile.txt";

$filename = $orig_filename . $_GET['input'];
$directory = dirname($filename);

symlink("/tmp/link1", $orig_filename);
symlink("/tmp/link2", $filename);
