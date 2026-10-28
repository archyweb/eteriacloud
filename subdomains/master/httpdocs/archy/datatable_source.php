<?php
$subdomain_root = dirname(dirname(__FILE__));
$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

// Keep Ajax diagnostics outside the public document root.
$datatableLog = $main_root . '/private/_common/datatables-error.log';
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (is_writable(dirname($datatableLog)) && (!file_exists($datatableLog) || is_writable($datatableLog))) {
    ini_set('error_log', $datatableLog);
}
error_reporting(E_ALL);

$datatableRequestId = uniqid('dt-', true);
$datatableBufferLevel = ob_get_level();
ob_start();

$datatableFailure = function ($message, $file, $line) use ($datatableRequestId, $datatableBufferLevel) {
    error_log('[' . $datatableRequestId . '] ' . $message . ' in ' . $file . ':' . $line);
    while (ob_get_level() > $datatableBufferLevel) {
        ob_end_clean();
    }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
    }
    echo json_encode([
        'error' => 'Impossibile caricare la tabella.',
        'request_id' => $datatableRequestId,
    ]);
};

register_shutdown_function(function () use ($datatableFailure) {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        $datatableFailure($error['message'], $error['file'], $error['line']);
    }
});

try {
    require $main_root . '/private/_common/assets/datatables/source.php';
    ob_end_flush();
} catch (Throwable $error) {
    $datatableFailure(get_class($error) . ': ' . $error->getMessage(), $error->getFile(), $error->getLine());
}
