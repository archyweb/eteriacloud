<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/datatable-pdf-job-error.log');
set_time_limit(45);
ini_set('memory_limit', '384M');
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
$db = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name']);
if ($db->connect_error) { json_out(['success'=>false,'message'=>'Errore connessione DB.'],500); }
$db->set_charset('utf8mb4');

$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
if (!$jobId) { json_out(['success'=>false,'message'=>'Job ID mancante.'],400); }
$res = $db->query("SELECT * FROM archy_jobs WHERE id={$jobId} AND job_type='datatable_pdf' LIMIT 1");
$job = $res ? $res->fetch_object() : null;
if (!$job) { json_out(['success'=>false,'message'=>'Job non trovato.'],404); }
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
$batchSize = isset($payload['batch_size']) ? max(10, min(60, (int)$payload['batch_size'])) : 40;
$processed = (int)$job->progress;
$total = count($ids);

$jobDir = $documentRoot . '/tmp/datatable_pdf_job_' . $jobId;
if (!is_dir($jobDir) && !@mkdir($jobDir, 0775, true)) {
    fail_job($db, $jobId, 'Impossibile creare cartella temporanea job.');
}

$db->query("UPDATE archy_jobs SET status='processing',started_at=IFNULL(started_at,NOW()),total={$total} WHERE id={$jobId}");

try {
    if ($processed < $total) {
        $batchIds = array_slice($ids, $processed, $batchSize);
        $batchNo = (int)floor($processed / $batchSize) + 1;
        $batchFile = $jobDir . '/batch_' . str_pad((string)$batchNo, 5, '0', STR_PAD_LEFT) . '.pdf';

        require_once $mainRoot . '/private/_common/classes/load_classes.php';
        $app = new App($documentRoot);
        $intent = preg_replace('/[^A-Za-z0-9_]/', '', (string)$params['intent']);
        require_once $mainRoot . '/private/_common/intents/' . $intent . '.php';
        $app->init();
        $GLOBALS['app'] = $app;

        $_GET = [];
        $_GET['intent'] = $intent;
        $_GET['format'] = 'pdf_batch';
        $_GET['columns'] = implode(',', array_map('strval', $params['columns']));
        $_GET['search'] = isset($params['search']) ? (string)$params['search'] : '';
        $_GET['order_by'] = isset($params['order_by']) ? (string)$params['order_by'] : '';
        $_GET['order'] = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';
        $_GET['filter'] = isset($params['filter']) && is_array($params['filter']) ? $params['filter'] : [];
        $_GET['sheet'] = isset($params['sheet']) ? (string)$params['sheet'] : 'A4';
        $_GET['orientation'] = isset($params['orientation']) ? (string)$params['orientation'] : 'L';
        $_GET['size'] = isset($params['size']) ? (int)$params['size'] : 7;
        $_GET['up'] = isset($params['up']) ? (int)$params['up'] : 8;
        $_GET['down'] = isset($params['down']) ? (int)$params['down'] : 8;
        $_GET['left'] = isset($params['left']) ? (int)$params['left'] : 6;
        $_GET['right'] = isset($params['right']) ? (int)$params['right'] : 6;
        $_GET['_export_ids'] = implode(',', $batchIds);
        $_GET['_batch_output'] = $batchFile;

        $app->export();

        $processed += count($batchIds);
        $safeBatch = $db->real_escape_string(basename($batchFile));
        $db->query("UPDATE archy_jobs SET progress={$processed},total={$total},log=CONCAT(IFNULL(log,''),'\nCreato {$safeBatch}: {$processed}/{$total}') WHERE id={$jobId}");
        json_out(['success'=>true,'status'=>'processing','progress'=>$processed,'total'=>$total]);
    }

    $batchFiles = glob($jobDir . '/batch_*.pdf');
    sort($batchFiles, SORT_NATURAL);
    if (!$batchFiles) { throw new Exception('Nessun batch PDF trovato per il merge finale.'); }

    $tcpdfPath = $mainRoot . '/private/_common/classes/tcpdf/tcpdf.php';
    $fpdiPath = $mainRoot . '/private/_common/classes/fpdi/src/autoload.php';
    if (!is_file($tcpdfPath) || !is_file($fpdiPath)) { throw new Exception('TCPDF o FPDI non trovati.'); }
    require_once $tcpdfPath;
    require_once $fpdiPath;

    $outDir = $documentRoot . '/assets/documents/datatable_exports';
    if (!is_dir($outDir) && !@mkdir($outDir, 0775, true)) { throw new Exception('Impossibile creare cartella export finale.'); }
    $outName = 'datatable_export_' . $jobId . '_' . date('Ymd_His') . '.pdf';
    $outFile = $outDir . '/' . $outName;

    $merged = new \setasign\Fpdi\Tcpdf\Fpdi();
    $merged->setPrintHeader(false);
    $merged->setPrintFooter(false);
    foreach ($batchFiles as $file) {
        $pages = $merged->setSourceFile($file);
        for ($pageNo=1; $pageNo <= $pages; $pageNo++) {
            $tpl = $merged->importPage($pageNo);
            $size = $merged->getTemplateSize($tpl);
            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
            $merged->AddPage($orientation, [$size['width'], $size['height']]);
            $merged->useTemplate($tpl);
        }
    }
    $merged->Output($outFile, 'F');
    unset($merged);
    if (!is_file($outFile) || filesize($outFile) <= 0) { throw new Exception('PDF finale non creato o vuoto.'); }

    foreach ($batchFiles as $file) { @unlink($file); }
    @rmdir($jobDir);
    $publicUrl = '/assets/documents/datatable_exports/' . $outName;
    $safeUrl = $db->real_escape_string($publicUrl);
    $db->query("UPDATE archy_jobs SET status='completed',progress={$total},total={$total},result_file='{$safeUrl}',finished_at=NOW(),log=CONCAT(IFNULL(log,''),'\nPDF DataTable completato') WHERE id={$jobId}");
    json_out(['success'=>true,'status'=>'completed','progress'=>$total,'total'=>$total,'result_file'=>$publicUrl]);

} catch (Throwable $e) {
    fail_job(
        $db,
        $jobId,
        $e->getMessage() .
        ' | File: ' . $e->getFile() .
        ' | Riga: ' . $e->getLine()
    );
}
