<?php
/* ARCHY DATATABLE PDF BATCH 100 + MERGE LINK-SAFE VERSION: 2026-07-22-1815 */
/* ARCHY DATATABLE WORKER VERSION: 2026-07-22-1545 */
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/datatable-pdf-job-error.log');
set_time_limit(75);
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

$lockFile = sys_get_temp_dir() . '/archy_datatable_pdf_' . $jobId . '.lock';
$lockHandle = fopen($lockFile, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    json_out(['success'=>true,'status'=>'busy','progress'=>(int)$job->progress,'total'=>(int)$job->total,'message'=>'Worker già in esecuzione.']);
}

register_shutdown_function(function() use ($db, $jobId, $lockHandle) {
    $last = error_get_last();
    if ($last && in_array($last['type'], [E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR], true)) {
        $msg = 'Errore PHP fatale: ' . $last['message'] . ' | File: ' . $last['file'] . ' | Riga: ' . $last['line'];
        $safe = $db->real_escape_string($msg);
        @$db->query("UPDATE archy_jobs SET status='failed',finished_at=NOW(),log=CONCAT(IFNULL(log,''),'\n', '{$safe}') WHERE id=".(int)$jobId);
        if (!headers_sent()) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); }
        if (ob_get_length()) { @ob_clean(); }
        echo json_encode(['success'=>false,'status'=>'failed','message'=>$msg], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
    if (is_resource($lockHandle)) { @flock($lockHandle, LOCK_UN); @fclose($lockHandle); }
});

$payload = json_decode($job->payload, true);
if (!is_array($payload) || empty($payload['ids']) || empty($payload['params'])) {
    fail_job($db, $jobId, 'Payload job non valido.');
}
$ids = array_values(array_filter(array_map('intval', $payload['ids'])));
$params = $payload['params'];
$batchSize = isset($payload['batch_size']) ? max(10, min(50, (int)$payload['batch_size'])) : 40; // batch piccoli e stabili
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

        /*
        |--------------------------------------------------------------------------
        | INIZIALIZZAZIONE APP
        |--------------------------------------------------------------------------
        | App::export() carica già l'intent ed esegue $app->init().
        | Non dobbiamo farlo anche qui: il doppio caricamento altera joins e
        | metadati delle colonne e può produrre stdClass dove è attesa una stringa.
        */
        $app = new App($documentRoot);
        $intent = preg_replace('/[^A-Za-z0-9_]/', '', (string)$params['intent']);
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

        $firstId = reset($batchIds);
        $lastId = end($batchIds);
        $memMb = round(memory_get_usage(true) / 1048576, 1);
        $db->query("UPDATE archy_jobs SET log=CONCAT(IFNULL(log,''),'\nAvvio batch {$batchNo}: offset {$processed}, ID {$firstId}-{$lastId}, memoria {$memMb} MB') WHERE id={$jobId}");

        $app->export();

        $processed += count($batchIds);
        $safeBatch = $db->real_escape_string(basename($batchFile));
        $db->query("UPDATE archy_jobs SET progress={$processed},total={$total},log=CONCAT(IFNULL(log,''),'\nCreato {$safeBatch}: {$processed}/{$total}') WHERE id={$jobId}");
        json_out(['success'=>true,'status'=>'processing','progress'=>$processed,'total'=>$total,'batch'=>$batchNo,'batch_size'=>count($batchIds)]);
    }

    $batchFiles = glob($jobDir . '/batch_*.pdf');
    sort($batchFiles, SORT_NATURAL);
    if (!$batchFiles) { throw new Exception('Nessun batch PDF trovato per il merge finale.'); }

    $outDir = $documentRoot . '/assets/documents/datatable_exports';
    if (!is_dir($outDir) && !@mkdir($outDir, 0775, true)) { throw new Exception('Impossibile creare cartella export finale.'); }
    $outName = 'datatable_export_' . $jobId . '_' . date('Ymd_His') . '.pdf';
    $outFile = $outDir . '/' . $outName;

    /*
    |--------------------------------------------------------------------------
    | MERGE PDF CON FPDI
    |--------------------------------------------------------------------------
    | FPDI è già installato nel gestionale e viene usato per accorpare i PDF
    | temporanei generati a batch. Con un solo batch manteniamo la copia diretta;
    | con più batch importiamo tutte le pagine nel PDF finale.
    */
    $mergeMethod = '';

    if (count($batchFiles) === 1) {
        if (!@copy($batchFiles[0], $outFile)) {
            throw new Exception('Impossibile copiare il batch singolo nel PDF finale.');
        }
        $mergeMethod = 'copy';
    } else {
        $tcpdfPath = $mainRoot . '/private/_common/classes/tcpdf/tcpdf.php';
        $fpdiPath = $mainRoot . '/private/_common/classes/fpdi/src/autoload.php';

        if (!is_file($tcpdfPath)) {
            throw new Exception('TCPDF non trovato: ' . $tcpdfPath);
        }

        if (!is_file($fpdiPath)) {
            throw new Exception('FPDI non trovato: ' . $fpdiPath);
        }

        require_once $tcpdfPath;
        require_once $fpdiPath;

        $merged = new \setasign\Fpdi\Tcpdf\Fpdi();
        $merged->setPrintHeader(false);
        $merged->setPrintFooter(false);
        $merged->SetMargins(0, 0, 0);
        $merged->SetAutoPageBreak(false, 0);

        foreach ($batchFiles as $file) {
            if (!is_file($file) || filesize($file) <= 0) {
                throw new Exception('Batch PDF non valido: ' . basename($file));
            }

            $pages = $merged->setSourceFile($file);

            for ($pageNo = 1; $pageNo <= $pages; $pageNo++) {
                $templateId = $merged->importPage($pageNo);
                $size = $merged->getTemplateSize($templateId);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                $merged->AddPage($orientation, [$size['width'], $size['height']]);
                $merged->useTemplate($templateId, 0, 0, $size['width'], $size['height'], true);
            }
        }

        $merged->Output($outFile, 'F');
        unset($merged);
        $mergeMethod = 'fpdi';
    }

    if (!is_file($outFile) || filesize($outFile) <= 0) { throw new Exception('PDF finale non creato o vuoto.'); }

    foreach ($batchFiles as $file) { @unlink($file); }
    @rmdir($jobDir);
    $publicUrl = '/assets/documents/datatable_exports/' . $outName;
    $safeUrl = $db->real_escape_string($publicUrl);
    $db->query("UPDATE archy_jobs SET status='completed',progress={$total},total={$total},result_file='{$safeUrl}',finished_at=NOW(),log=CONCAT(IFNULL(log,''),'\nPDF DataTable completato - merge: {$mergeMethod}') WHERE id={$jobId}");
    json_out(['success'=>true,'status'=>'completed','progress'=>$total,'total'=>$total,'result_file'=>$publicUrl,'merge_method'=>$mergeMethod]);

} catch (Throwable $e) {
    fail_job(
        $db,
        $jobId,
        $e->getMessage() .
        ' | File: ' . $e->getFile() .
        ' | Riga: ' . $e->getLine() .
        ' | Worker: 2026-07-22-1545'
    );
}
