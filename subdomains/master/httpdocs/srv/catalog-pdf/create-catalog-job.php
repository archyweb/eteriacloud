<?php

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/catalog-job-error.log');

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| BOOTSTRAP CONFIG STANDALONE
|--------------------------------------------------------------------------
| config-local.php usa ArchyEnv. Gli endpoint sotto /srv non passano dal
| normale index.php, quindi carichiamo prima le classi comuni.
*/
$documentRoot = dirname(dirname(__DIR__));
$mainRoot = dirname(dirname(dirname($documentRoot)));

if (!class_exists('ArchyEnv')) {
    $loadClasses = $mainRoot . '/private/_common/classes/load_classes.php';

    if (!is_file($loadClasses)) {
        throw new RuntimeException('Bootstrap gestionale non trovato: ' . $loadClasses);
    }

    require_once $loadClasses;
}

$configFile = $documentRoot . '/config-local.php';

if (!is_file($configFile)) {
    throw new RuntimeException('config-local.php non trovato: ' . $configFile);
}

$config = require $configFile;

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