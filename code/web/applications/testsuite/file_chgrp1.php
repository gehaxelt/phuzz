<?php

$orig_dir = "./testdir/";
$orig_file = $orig_dir . "origfile.txt";

$filename = $orig_file . $_GET['input'];
$directory = dirname($filename);

chgrp($orig_file, 0);
chgrp($filename, 0);
