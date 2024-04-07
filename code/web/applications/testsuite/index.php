<?php

echo "foo";

$d = [];
for ($i = 0; $i < 10; $i++) {
    $d[] = $i * 42;
}

if (isset($_GET['foo'])) {
    echo "foo is set";
    if ($_GET['foo'] == 13) { //13
        echo "Foo is 13";
        include "flag.php";

        give_flag();
    } else {
        include "include.php";
    }
} else {
    echo "foo is not set";
}

if (isset($_GET["foo"]) && isset($_GET["bar"])) {
    echo $_GET["bar"];
}

if (isset($_GET["foo"]) && isset($_GET["bar"])) {
    if ($_GET["foo"] == 16 && $_GET["bar"] == 17) {
        echo $_GET["foo"] + $_GET["bar"];
    }
}

if (isset($_GET["foo"]) && isset($_GET["bar"])) {
    if ($_GET["foo"] == 16 && $_GET["bar"] == 25) {
        echo ($_GET["foo"] + $_GET["bar"]) / 2;
    }
    if ($_GET["foo"] > 123 && $_GET["bar"] < 456) {
        echo $_GET["foo"] + $_GET["bar"];
    }
}
