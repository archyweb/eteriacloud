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
$debug = !empty($_GET['debug']);

if (!$google_calendar->isConfigured()) {
    if ($debug) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Google Calendar OAuth debug\n";
        echo "configured: no\n";
        echo "host: " . ($_SERVER['HTTP_HOST'] ?? '') . "\n";
        echo "protocol: " . ($app->protocol ?? '') . "\n";
        echo "redirect_uri: " . $google_calendar->getRedirectUri() . "\n";
        echo "local_callback_uri: " . $google_calendar->getLocalCallbackUri() . "\n";
        exit;
    }

    set_message('warning', 'Google Calendar non e ancora configurato.');
    header('location: /archy/app.php?view=dashboard');
    exit;
}

$state = bin2hex(random_bytes(32));
$_SESSION['google_calendar_oauth_state'] = $state;

$auth_url = $google_calendar->buildAuthUrl($google_calendar->buildState($state));

if ($debug) {
    $auth_parts = parse_url($auth_url);
    $auth_params = [];

    if (!empty($auth_parts['query'])) {
        parse_str($auth_parts['query'], $auth_params);
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo "Google Calendar OAuth debug\n";
    echo "configured: yes\n";
    echo "host: " . ($_SERVER['HTTP_HOST'] ?? '') . "\n";
    echo "protocol: " . ($app->protocol ?? '') . "\n";
    echo "redirect_uri: " . $google_calendar->getRedirectUri() . "\n";
    echo "local_callback_uri: " . $google_calendar->getLocalCallbackUri() . "\n";
    echo "auth_host: " . ($auth_parts['host'] ?? '') . "\n";
    echo "auth_client_id_present: " . (!empty($auth_params['client_id']) ? 'yes' : 'no') . "\n";
    echo "auth_redirect_uri: " . ($auth_params['redirect_uri'] ?? '') . "\n";
    echo "auth_scope: " . ($auth_params['scope'] ?? '') . "\n";
    echo "auth_response_type: " . ($auth_params['response_type'] ?? '') . "\n";
    echo "auth_access_type: " . ($auth_params['access_type'] ?? '') . "\n";
    exit;
}

header('location: ' . $auth_url);
exit;
