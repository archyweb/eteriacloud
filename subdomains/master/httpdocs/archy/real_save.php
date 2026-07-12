<?php

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);

echo $app->store(true);