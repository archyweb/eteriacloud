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

$expected_nonce = $_SESSION['google_calendar_oauth_state'] ?? '';
$received_state = $_GET['state'] ?? '';
unset($_SESSION['google_calendar_oauth_state']);

$google_calendar = new GoogleCalendarService($app);

try {
    $state_payload = $google_calendar->parseState($received_state);
} catch (Exception $e) {
    set_message('danger', 'Richiesta Google Calendar non valida: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    header('location: /archy/app.php?view=dashboard');
    exit;
}

$received_nonce = $state_payload['nonce'] ?? '';

if ($expected_nonce === '' || !hash_equals($expected_nonce, $received_nonce)) {
    set_message('danger', 'Richiesta Google Calendar non valida.');
    header('location: /archy/app.php?view=dashboard');
    exit;
}

if (!empty($_GET['error'])) {
    set_message('warning', 'Collegamento Google Calendar annullato.');
    header('location: /archy/app.php?view=dashboard');
    exit;
}

if (empty($_GET['code'])) {
    set_message('danger', 'Codice OAuth Google Calendar mancante.');
    header('location: /archy/app.php?view=dashboard');
    exit;
}

try {
    $account = $google_calendar->handleCallback($_GET['code']);
    $email = $account && !empty($account['google_email']) ? ' (' . htmlspecialchars($account['google_email'], ENT_QUOTES, 'UTF-8') . ')' : '';

    set_message('success', 'Google Calendar collegato correttamente' . $email . '.');
} catch (Exception $e) {
    set_message('danger', 'Errore collegamento Google Calendar: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

header('location: /archy/app.php?view=dashboard');
exit;
