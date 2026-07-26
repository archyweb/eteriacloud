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

if (!$google_calendar->isConfigured()) {
    set_message('warning', 'Google Calendar non e ancora configurato.');
    header('location: /archy/app.php?view=dashboard');
    exit;
}

$state = bin2hex(random_bytes(32));
$_SESSION['google_calendar_oauth_state'] = $state;

header('location: ' . $google_calendar->buildAuthUrl($google_calendar->buildState($state)));
exit;
