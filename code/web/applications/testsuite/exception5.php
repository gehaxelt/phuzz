<?php

function myexceptionhandler(Throwable $exception) {
	echo "My exception handler.";
}

function myexception() {
	throw new Exception("from function");
}

set_exception_handler('myexceptionhandler');
restore_exception_handler();

myexception();
