<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/datatable-excel-job-error.log');
header('Content-Type: application/json; charset=utf-8');

function json_out($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$documentRoot = dirname(dirname(__DIR__));
$mainRoot = dirname(dirname(dirname($documentRoot)));

if (!class_exists('ArchyEnv')) {
    $loadClasses = $mainRoot . '/private/_common/classes/load_classes.php';
    if (!is_file($loadClasses)) {
        json_out(['success' => false, 'message' => 'Bootstrap gestionale non trovato.'], 500);
    }
    require_once $loadClasses;
}

$configFile = $documentRoot . '/config-local.php';
if (!is_file($configFile)) {
    json_out(['success' => false, 'message' => 'config-local.php non trovato.'], 500);
}

$config = require $configFile;
$db = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
if ($db->connect_error) {
    json_out(['success' => false, 'message' => 'Errore connessione DB.'], 500);
}
$db->set_charset('utf8mb4');

$ids = [];
if (!empty($_POST['ids'])) {
    $ids = array_values(array_unique(array_filter(array_map('intval', explode(',', (string)$_POST['ids'])))));
}
if (!$ids) {
    json_out(['success' => false, 'message' => 'Nessun ID ricevuto.'], 400);
}

$params = [];
if (!empty($_POST['params'])) {
    $params = json_decode((string)$_POST['params'], true);
}
if (!is_array($params)) {
    json_out(['success' => false, 'message' => 'Parametri export non validi.'], 400);
}

$intent = isset($params['intent']) ? preg_replace('/[^A-Za-z0-9_]/', '', (string)$params['intent']) : '';
$columns = isset($params['columns']) && is_array($params['columns']) ? $params['columns'] : [];
$columns = array_values(array_filter(array_map('strval', $columns)));
if ($intent === '' || !$columns) {
    json_out(['success' => false, 'message' => 'Intent o colonne mancanti.'], 400);
}

$payload = [
    'ids' => $ids,
    'params' => $params,
    'batch_size' => 150
];
$payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$total = count($ids);

$stmt = $db->prepare("INSERT INTO archy_jobs (job_type,status,progress,total,payload,log,created_at) VALUES ('datatable_excel','pending',0,?,?,'Excel DataTable messo in coda',NOW())");
if (!$stmt) {
    json_out(['success' => false, 'message' => 'Errore prepare SQL: '.$db->error], 500);
}
$stmt->bind_param('is', $total, $payloadJson);
if (!$stmt->execute()) {
    json_out(['success' => false, 'message' => 'Errore creazione job: '.$stmt->error], 500);
}

json_out([
    'success' => true,
    'job_id' => (int)$stmt->insert_id,
    'total' => $total,
    'message' => 'Excel DataTable messo in coda.'
]);
