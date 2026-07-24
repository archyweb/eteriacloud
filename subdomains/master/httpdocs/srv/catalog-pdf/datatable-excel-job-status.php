<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$documentRoot = dirname(dirname(__DIR__));
$mainRoot = dirname(dirname(dirname($documentRoot)));

if (!class_exists('ArchyEnv')) {
    $loadClasses = $mainRoot . '/private/_common/classes/load_classes.php';
    if (!is_file($loadClasses)) {
        echo json_encode(['success'=>false,'message'=>'Bootstrap gestionale non trovato.']);
        exit;
    }
    require_once $loadClasses;
}

$configFile = $documentRoot . '/config-local.php';
if (!is_file($configFile)) {
    echo json_encode(['success'=>false,'message'=>'config-local.php non trovato.']);
    exit;
}

$config = require $configFile;
$db = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
if ($db->connect_error) {
    echo json_encode(['success'=>false,'message'=>'Errore connessione DB.']);
    exit;
}
$db->set_charset('utf8mb4');

$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
if (!$jobId) {
    echo json_encode(['success'=>false,'message'=>'Job ID mancante.']);
    exit;
}

$res = $db->query("SELECT id,job_type,status,progress,total,result_file,log,created_at,started_at,finished_at FROM archy_jobs WHERE id={$jobId} AND job_type='datatable_excel' LIMIT 1");
$job = $res ? $res->fetch_object() : null;
if (!$job) {
    echo json_encode(['success'=>false,'message'=>'Job non trovato.']);
    exit;
}

echo json_encode(['success'=>true,'job'=>$job], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
