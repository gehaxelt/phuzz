<?php

/*

bWAPP, or a buggy web application, is a free and open source deliberately insecure web application.
It helps security enthusiasts, developers and students to discover and to prevent web vulnerabilities.
bWAPP covers all major known web vulnerabilities, including all risks from the OWASP Top 10 project!
It is for security-testing and educational purposes only.

Enjoy!

Malik Mesellem
Twitter: @MME_IT

bWAPP is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License (http://creativecommons.org/licenses/by-nc-nd/4.0/). Copyright © 2014 MME BVBA. All rights reserved.

*/

// Connection settings
include("config.inc.php");

// Connects to the server
$link = mysqli_connect($server, $username, $password, $database);

// Checks the connection
if(!$link)
{

    // @mail($recipient, "Could not connect to server: ", mysqli_error());

    die("Could not connect to the server: " . mysqli_error($link));

}

// Connects to the database
//$database = mysqli_select_db($database, $link);

// Checks the connection
//if(!$database)
//{

    // @mail($recipient, "Could not connect to database: ", mysqli_error());

    //die("Could not connect to the database: " . mysqli_error());

//}

// mysqli_close($link);

?>