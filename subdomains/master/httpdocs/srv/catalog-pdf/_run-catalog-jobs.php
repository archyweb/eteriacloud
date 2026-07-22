<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

$config = require dirname(dirname(__DIR__)) . '/config-local.php';

$db = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($db->connect_error) {
    die('Errore connessione DB');
}

$jobId = 0;

if (isset($argv[1])) {
    $jobId = (int) $argv[1];
} elseif (isset($_GET['job_id'])) {
    $jobId = (int) $_GET['job_id'];
}

if ($jobId > 0) {
    $job = $db->query("
        SELECT *
        FROM archy_jobs
        WHERE id = {$jobId}
        AND status = 'pending'
        LIMIT 1
    ")->fetch_object();
} else {
    $job = $db->query("
        SELECT *
        FROM archy_jobs
        WHERE job_type = 'catalog_pdf'
        AND status = 'pending'
        ORDER BY id ASC
        LIMIT 1
    ")->fetch_object();
}

if (!$job) {
    echo "Nessun job da eseguire.\n";
    exit;
}

$db->query("
    UPDATE archy_jobs
    SET status = 'processing',
        started_at = NOW(),
        log = CONCAT(IFNULL(log, ''), '\nAvvio generazione Catalogo PDF')
    WHERE id = " . (int)$job->id
);

$payload = json_decode($job->payload, true);
$ids = $payload['ids'] ?? [];

if (empty($ids)) {
    $db->query("
        UPDATE archy_jobs
        SET status = 'failed',
            finished_at = NOW(),
            log = CONCAT(IFNULL(log, ''), '\nNessun ID nel payload')
        WHERE id = " . (int)$job->id
    );
    exit;
}

$_GET['ids'] = implode(',', $ids);
$_GET['job_id'] = $job->id;

$_REQUEST['ids'] = $_GET['ids'];
$_REQUEST['job_id'] = $_GET['job_id'];

try {
    require __DIR__ . '/merge-catalogo-job.php';
} catch (Throwable $e) {

    $error = $db->real_escape_string($e->getMessage());

    $db->query("
        UPDATE archy_jobs
        SET status = 'failed',
            finished_at = NOW(),
            log = CONCAT(IFNULL(log, ''), '\nErrore: {$error}')
        WHERE id = " . (int)$job->id
    );

    echo "Errore job: " . $e->getMessage();
}