<?php
/* ARCHY DATATABLE EXCEL JOB - BATCH APPEND VERSION: 2026-07-23 */
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/datatable-excel-job-error.log');
set_time_limit(60);
ini_set('memory_limit', '512M');
ob_start();
header('Content-Type: application/json; charset=utf-8');

function json_out($data, $code = 200) {
    if (ob_get_length()) { ob_clean(); }
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fail_job($db, $jobId, $message) {
    $safe = $db->real_escape_string($message);
    $db->query("UPDATE archy_jobs SET status='failed',finished_at=NOW(),log=CONCAT(IFNULL(log,''),'\nErrore: {$safe}') WHERE id=".(int)$jobId);
    json_out(['success'=>false,'status'=>'failed','message'=>$message], 500);
}

$documentRoot = dirname(dirname(__DIR__));
$mainRoot = dirname(dirname(dirname($documentRoot)));

if (!class_exists('ArchyEnv')) {
    $loadClasses = $mainRoot . '/private/_common/classes/load_classes.php';
    if (!is_file($loadClasses)) {
        json_out(['success'=>false,'message'=>'Bootstrap gestionale non trovato.'], 500);
    }
    require_once $loadClasses;
}

$configFile = $documentRoot . '/config-local.php';
if (!is_file($configFile)) {
    json_out(['success'=>false,'message'=>'config-local.php non trovato.'], 500);
}

$config = require $configFile;
$db = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
if ($db->connect_error) {
    json_out(['success'=>false,'message'=>'Errore connessione DB.'], 500);
}
$db->set_charset('utf8mb4');

$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
if (!$jobId) {
    json_out(['success'=>false,'message'=>'Job ID mancante.'], 400);
}

$res = $db->query("SELECT * FROM archy_jobs WHERE id={$jobId} AND job_type='datatable_excel' LIMIT 1");
$job = $res ? $res->fetch_object() : null;
if (!$job) {
    json_out(['success'=>false,'message'=>'Job non trovato.'], 404);
}
if ($job->status === 'completed') {
    json_out(['success'=>true,'status'=>'completed','progress'=>(int)$job->progress,'total'=>(int)$job->total,'result_file'=>$job->result_file]);
}
if ($job->status === 'failed') {
    json_out(['success'=>false,'status'=>'failed','progress'=>(int)$job->progress,'total'=>(int)$job->total,'message'=>$job->log]);
}

$payload = json_decode($job->payload, true);
if (!is_array($payload) || empty($payload['ids']) || empty($payload['params'])) {
    fail_job($db, $jobId, 'Payload job non valido.');
}

$ids = array_values(array_filter(array_map('intval', $payload['ids'])));
$params = $payload['params'];
$batchSize = max(25, min(300, (int)($payload['batch_size'] ?? 150)));
$processed = (int)$job->progress;
$total = count($ids);

$outDir = $documentRoot . '/assets/documents/datatable_exports';
if (!is_dir($outDir) && !@mkdir($outDir, 0775, true)) {
    fail_job($db, $jobId, 'Impossibile creare cartella export finale.');
}

$outName = '';
$outFile = '';
if (!empty($job->result_file)) {
    $outName = basename((string)$job->result_file);
    $outFile = $outDir . '/' . $outName;
} else {
    $safeIntent = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)($params['intent'] ?? 'export'));
    $outName = 'datatable_' . $safeIntent . '_' . $jobId . '_' . date('Ymd_His') . '.xlsx';
    $outFile = $outDir . '/' . $outName;
    $publicUrl = '/assets/documents/datatable_exports/' . $outName;
    $safeUrl = $db->real_escape_string($publicUrl);
    $db->query("UPDATE archy_jobs SET result_file='{$safeUrl}' WHERE id={$jobId}");
}

$db->query("UPDATE archy_jobs SET status='processing',started_at=IFNULL(started_at,NOW()),total={$total} WHERE id={$jobId}");

try {
    if ($processed < $total) {
        $batchIds = array_slice($ids, $processed, $batchSize);
        if (!$batchIds) {
            throw new RuntimeException('Batch Excel vuoto.');
        }

        require_once $mainRoot . '/private/_common/classes/load_classes.php';
        $app = new App($documentRoot);
        $intent = preg_replace('/[^A-Za-z0-9_]/', '', (string)$params['intent']);
        $GLOBALS['app'] = $app;

        $_GET = [];
        $_GET['intent'] = $intent;
        $_GET['format'] = 'excel_job';
        $_GET['columns'] = implode(',', array_map('strval', $params['columns']));
        // Gli ID sono già la fotografia esatta della DataTable filtrata.
        // Non riapplichiamo filtri/search per evitare differenze tra query DataTable ed export.
        $_GET['search'] = '';
        $_GET['order_by'] = isset($params['order_by']) ? (string)$params['order_by'] : '';
        $_GET['order'] = isset($params['order']) && strtoupper((string)$params['order']) === 'DESC' ? 'DESC' : 'ASC';
        $_GET['filter'] = [];
        $_GET['_export_ids'] = implode(',', $batchIds);
        $_GET['_excel_output'] = $outFile;
        $_GET['_excel_append'] = $processed > 0 ? 1 : 0;

        $app->export();

        if (!is_file($outFile) || filesize($outFile) <= 0) {
            throw new RuntimeException('File Excel batch non creato o vuoto.');
        }

        $processed += count($batchIds);
        $db->query("UPDATE archy_jobs SET progress={$processed},total={$total},log=CONCAT(IFNULL(log,''),'\nBatch Excel: {$processed}/{$total}') WHERE id={$jobId}");

        if ($processed < $total) {
            json_out(['success'=>true,'status'=>'processing','progress'=>$processed,'total'=>$total]);
        }
    }

    $publicUrl = '/assets/documents/datatable_exports/' . $outName;
    $safeUrl = $db->real_escape_string($publicUrl);
    $db->query("UPDATE archy_jobs SET status='completed',progress={$total},total={$total},result_file='{$safeUrl}',finished_at=NOW(),log=CONCAT(IFNULL(log,''),'\nExcel DataTable completato') WHERE id={$jobId}");
    json_out(['success'=>true,'status'=>'completed','progress'=>$total,'total'=>$total,'result_file'=>$publicUrl]);

} catch (Throwable $e) {
    fail_job($db, $jobId, $e->getMessage() . ' | File: ' . $e->getFile() . ' | Riga: ' . $e->getLine());
}
