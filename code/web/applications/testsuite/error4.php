<?php

function myerrorhandler($errno, $errstr, $errfile, $errline) {
	echo "From another error handler";
}

set_error_handler('myerrorhandler');

echo $foobar;
