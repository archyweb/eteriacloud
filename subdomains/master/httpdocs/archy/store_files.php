<?php

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);

try {
    $result = $app->store_files();
    echo json_encode($result);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit;
