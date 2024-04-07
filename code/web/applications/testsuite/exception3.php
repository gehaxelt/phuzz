<?php
function myexception() {
	throw new Exception("from function");
}

@myexception(); 
