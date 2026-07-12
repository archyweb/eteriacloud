<?php

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/catalog-job-error.log');

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

$config = require dirname(dirname(__DIR__)) . '/config-local.php';

$db = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($db->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Errore connessione DB.'
    ]);
    exit;
}

$ids = [];

if (!empty($_POST['ids'])) {
    $ids = array_filter(array_map('intval', explode(',', $_POST['ids'])));
}

if (empty($ids)) {
    echo json_encode([
        'success' => false,
        'message' => 'Nessun ID ricevuto.'
    ]);
    exit;
}

$payload = json_encode([
    'ids' => array_values($ids)
]);

$total = count($ids);

$stmt = $db->prepare("
    INSERT INTO archy_jobs
    SET job_type = 'catalog_pdf',
        status = 'pending',
        progress = 0,
        total = ?,
        payload = ?,
        created_at = NOW()
");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Errore prepare SQL: ' . $db->error
    ]);
    exit;
}
$stmt->bind_param('is', $total, $payload);
$stmt->execute();

$jobId = $stmt->insert_id;

/*
|--------------------------------------------------------------------------
| AVVIO WORKER IN BACKGROUND SENZA CRON
|--------------------------------------------------------------------------
*/

echo json_encode([
    'success' => true,
    'message' => 'Catalogo PDF messo in coda.',
    'job_id' => $jobId,
    'total' => $total
]);

if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

$_GET['job_id'] = $jobId;

require __DIR__ . '/run-catalog-jobs.php';

exit;