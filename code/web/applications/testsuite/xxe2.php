<?php
$user_input = $_GET['input'];

$doc = new DOMDocument();
$doc->loadXML($user_input, LIBXML_NOENT); // this is not secure