<?php

session_start();

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));

require_once $main_root . '/private/_common/classes/load_classes.php';

$app = new App($subdomain_root);

if (!$app->isLogged()) {
    header('location: /archy/login.php');
    exit;
}

$google_calendar = new GoogleCalendarService($app);
$google_calendar->disconnect();

set_message('success', 'Google Calendar scollegato.');
header('location: /archy/app.php?view=dashboard');
exit;
