<?php

ini_set('display_errors', 1);
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

$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

if (!$jobId) {
    echo json_encode([
        'success' => false,
        'message' => 'Job ID mancante.'
    ]);
    exit;
}

$job = $db->query("
    SELECT id, status, progress, total, result_file, log, created_at, started_at, finished_at
    FROM archy_jobs
    WHERE id = {$jobId}
    LIMIT 1
")->fetch_object();

if (!$job) {
    echo json_encode([
        'success' => false,
        'message' => 'Job non trovato.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'job' => $job
]);

exit;