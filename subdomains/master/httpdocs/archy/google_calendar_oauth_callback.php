<?php

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

require_once $main_root . '/private/_common/classes/load_classes.php';

$state = $_GET['state'] ?? '';

try {
    $payload = GoogleCalendarService::decodeStateForCentralCallback($state);
    $return_uri = $payload['return_uri'] ?? '';

    if (!preg_match('#^https://[A-Za-z0-9.-]+/archy/google_calendar_callback\.php$#', $return_uri)) {
        throw new Exception('Return URI Google Calendar non valido.');
    }

    $params = [
        'state' => $state
    ];

    if (!empty($_GET['code'])) {
        $params['code'] = $_GET['code'];
    }

    if (!empty($_GET['error'])) {
        $params['error'] = $_GET['error'];
    }

    header('location: ' . $return_uri . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    exit;
} catch (Exception $e) {
    header('Content-Type: text/plain; charset=utf-8', true, 400);
    echo 'Errore callback Google Calendar: ' . $e->getMessage();
    exit;
}
