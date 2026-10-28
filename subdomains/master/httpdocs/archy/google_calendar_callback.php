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

function google_calendar_finish($popup = false)
{
    if ($popup) {
        header('Content-Type: text/html; charset=utf-8');
        ?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <title>Google Calendar</title>
</head>
<body>
    <p>Collegamento Google Calendar completato. Puoi chiudere questa finestra.</p>
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.location.href = '/archy/app.php?view=dashboard';
        }
        window.close();
    </script>
    <p><a href="/archy/app.php?view=dashboard">Torna alla dashboard</a></p>
</body>
</html>
        <?php
        exit;
    }

    header('location: /archy/app.php?view=dashboard');
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
$is_popup = !empty($state_payload['popup']);
$state_user_id = (int)($state_payload['user_id'] ?? 0);
$state_created_at = (int)($state_payload['created_at'] ?? 0);
$state_is_recent = $state_created_at > 0 && $state_created_at >= time() - 900;
$state_matches_user = $state_user_id > 0 && $state_user_id === (int)$app->user_id;
$session_nonce_matches = $expected_nonce !== '' && hash_equals($expected_nonce, $received_nonce);

if (!$session_nonce_matches && (!$state_matches_user || !$state_is_recent)) {
    set_message('danger', 'Richiesta Google Calendar non valida.');
    google_calendar_finish($is_popup);
}

if (!empty($_GET['error'])) {
    set_message('warning', 'Collegamento Google Calendar annullato.');
    google_calendar_finish($is_popup);
}

if (empty($_GET['code'])) {
    set_message('danger', 'Codice OAuth Google Calendar mancante.');
    google_calendar_finish($is_popup);
}

try {
    $account = $google_calendar->handleCallback($_GET['code']);
    $email = $account && !empty($account['google_email']) ? ' (' . htmlspecialchars($account['google_email'], ENT_QUOTES, 'UTF-8') . ')' : '';

    set_message('success', 'Google Calendar collegato correttamente' . $email . '.');
} catch (Exception $e) {
    set_message('danger', 'Errore collegamento Google Calendar: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

google_calendar_finish($is_popup);
