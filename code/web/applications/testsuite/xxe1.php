<?php
$user_input = $_GET['input'];

$doc = new DOMDocument();
$doc->loadXML($user_input); // this is secure