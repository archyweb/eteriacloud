<?php

header('Content-Type: application/json; charset=utf-8');

$subdomain_root = realpath(__DIR__ . '/../..');
$main_root = realpath($subdomain_root . '/../../..');
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

function core_update_web_log($mainRoot, $message)
{
    if (!$mainRoot) {
        return;
    }

    $dir = $mainRoot . '/private/_common/update/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    @file_put_contents(
        $dir . '/web-run-' . date('Y-m-d') . '.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
        FILE_APPEND
    );
}

function core_update_web_response($statusCode, $payload)
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

register_shutdown_function(function () use ($main_root, $debug) {
    $error = error_get_last();
    if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        return;
    }

    core_update_web_log($main_root, 'Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode(array(
        'status' => 'error',
        'message' => 'Errore fatale PHP durante il test aggiornamenti.',
        'debug' => $debug ? $error : null,
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

if (!$subdomain_root || !$main_root) {
    core_update_web_response(500, array(
        'status' => 'error',
        'message' => 'Root gestionale non rilevata.',
    ));
}

$configPath = $subdomain_root . '/config-local.php';
$config = is_file($configPath) ? require $configPath : array();
$expectedToken = isset($config['core_update_web_token']) ? trim((string) $config['core_update_web_token']) : '';
$requestToken = isset($_GET['token']) ? trim((string) $_GET['token']) : '';

if ($expectedToken === '') {
    core_update_web_response(403, array(
        'status' => 'error',
        'message' => 'Token web aggiornamenti non configurato.',
    ));
}

if (!hash_equals($expectedToken, $requestToken)) {
    core_update_web_response(403, array(
        'status' => 'error',
        'message' => 'Token non valido.',
    ));
}

core_update_web_log($main_root, 'Avvio web-run dry_run=' . (isset($_GET['dry_run']) ? $_GET['dry_run'] : 'default'));

$dryRun = !isset($_GET['dry_run']) || !in_array(strtolower((string) $_GET['dry_run']), array('0', 'false', 'no'), true);
$allowLive = isset($config['core_update_web_allow_live']) && $config['core_update_web_allow_live'] === true;

if (!$dryRun && !$allowLive) {
    core_update_web_response(403, array(
        'status' => 'error',
        'message' => 'Aggiornamento live da browser non abilitato. Usa dry_run=1 oppure CLI.',
    ));
}

$options = array(
    'main_root' => $main_root,
    'subdomain_root' => $subdomain_root,
    'manifest' => isset($_GET['manifest']) ? $_GET['manifest'] : 'private/_common/update/manifest.json',
    'customers' => isset($_GET['customers']) ? $_GET['customers'] : 'private/_common/update/customers.csv',
    'dry_run' => $dryRun,
    'mode' => isset($_GET['mode']) ? $_GET['mode'] : 'db',
);

try {
    require_once $main_root . '/private/_common/classes/load_classes.php';

    $updater = new CoreUpdate($options);
    $result = $updater->run($options);
    core_update_web_log($main_root, 'Completato web-run status=' . (isset($result['status']) ? $result['status'] : 'unknown'));
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    core_update_web_log($main_root, get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    core_update_web_response(500, array(
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug' => $debug ? array(
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ) : null,
    ));
}
