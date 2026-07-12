<?php

include(dirname(__FILE__)."/Config.php");
include(dirname(__FILE__)."/Auth.php");

$dbh = new PDO("mysql:host=mysql;dbname=database", "root", "");

$config = new PHPAuth\Config($dbh);
$auth   = new PHPAuth\Auth($dbh, $config);

/*
if (!$auth->isLogged()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Forbidden";

    exit();
}
*/