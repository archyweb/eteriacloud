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
$debug_can_show_live_url = !empty($app->is_admin);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

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

$signed_state = $google_calendar->buildState($state);
$auth_url = $google_calendar->buildAuthUrl($signed_state);

if ($debug) {
    $auth_parts = parse_url($auth_url);
    $auth_params = [];

    if (!empty($auth_parts['query'])) {
        parse_str($auth_parts['query'], $auth_params);
    }

    $auth_test_params = $auth_params;
    $auth_test_params['state'] = 'DEBUG_STATE';
    $auth_test_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($auth_test_params, '', '&', PHP_QUERY_RFC3986);
    $state_payload = [];

    try {
        $state_payload = $google_calendar->parseState($signed_state);
    } catch (Exception $e) {
        $state_payload = ['error' => $e->getMessage()];
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo "Google Calendar OAuth debug\n";
    echo "configured: yes\n";
    echo "host: " . ($_SERVER['HTTP_HOST'] ?? '') . "\n";
    echo "protocol: " . ($app->protocol ?? '') . "\n";
    echo "redirect_uri: " . $google_calendar->getRedirectUri() . "\n";
    echo "local_callback_uri: " . $google_calendar->getLocalCallbackUri() . "\n";
    echo "state_return_uri: " . ($state_payload['return_uri'] ?? '') . "\n";
    echo "state_nonce_present: " . (!empty($state_payload['nonce']) ? 'yes' : 'no') . "\n";
    echo "state_error: " . ($state_payload['error'] ?? '') . "\n";
    echo "auth_host: " . ($auth_parts['host'] ?? '') . "\n";
    echo "auth_client_id_present: " . (!empty($auth_params['client_id']) ? 'yes' : 'no') . "\n";
    echo "auth_client_id_tail: " . (!empty($auth_params['client_id']) ? substr($auth_params['client_id'], -40) : '') . "\n";
    echo "auth_redirect_uri: " . ($auth_params['redirect_uri'] ?? '') . "\n";
    echo "auth_scope: " . ($auth_params['scope'] ?? '') . "\n";
    echo "auth_response_type: " . ($auth_params['response_type'] ?? '') . "\n";
    echo "auth_access_type: " . ($auth_params['access_type'] ?? '') . "\n";
    echo "auth_prompt: " . ($auth_params['prompt'] ?? '') . "\n";
    echo "auth_test_url: " . $auth_test_url . "\n";
    echo "auth_live_url: " . ($debug_can_show_live_url ? $auth_url : 'hidden') . "\n";
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <title>Collegamento Google Calendar</title>
    <link rel="stylesheet" href="/admin_template/vendors/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="mx-auto bg-white border rounded p-4" style="max-width: 520px;">
            <h1 class="h5 mb-3">Collegamento Google Calendar</h1>
            <p class="text-muted">Continua su Google per autorizzare la sincronizzazione del calendario.</p>
            <p class="small text-muted">Richiesta generata alle <?php echo date('d/m/Y H:i:s'); ?></p>
            <a class="btn btn-primary" href="<?php echo htmlspecialchars($auth_url, ENT_QUOTES, 'UTF-8'); ?>">Continua con Google</a>
            <a class="btn btn-link" href="/archy/app.php?view=dashboard">Annulla</a>
        </div>
    </main>
</body>
</html>
<?php
exit;
