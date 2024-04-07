<?php

##########################################################################################
#                               bWAPP auth function overrides                               #
##########################################################################################

if(!isset($_SESSION['login'])) {
	session_start();
	session_regenerate_id(true);
	$token = sha1(uniqid(mt_rand(0,100000)));

	$_SESSION["login"] = "admin";
	$_SESSION["admin"] = "1";
	$_SESSION["token"] = $token;
	$_SESSION["amount"] = 1000;

	if(!isset($_COOKIE['security_level'])) {
		$_COOKIE['security_level'] = "0";
        setcookie("security_level", "0", time()+60*60*24*365, "/", "", false, false);
	}
}

?>