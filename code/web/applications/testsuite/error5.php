<?php

function myerrorhandler($errno, $errstr, $errfile, $errline) {
	echo "From another error handler";
}

set_error_handler('myerrorhandler');
restore_error_handler();

echo $foobar;
