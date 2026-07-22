<?php
/* ARCHY DATATABLE PDF BATCH 100 + MERGE LINK-SAFE VERSION: 2026-07-22-1815 */
/* ARCHY DATATABLE WORKER VERSION: 2026-07-22-1545 */
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
$batchSize = 100; // massimo 100 opere per PDF temporaneo
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

        $app->export();

        $processed += count($batchIds);
        $safeBatch = $db->real_escape_string(basename($batchFile));
        $db->query("UPDATE archy_jobs SET progress={$processed},total={$total},log=CONCAT(IFNULL(log,''),'\nCreato {$safeBatch}: {$processed}/{$total}') WHERE id={$jobId}");
        json_out(['success'=>true,'status'=>'processing','progress'=>$processed,'total'=>$total]);
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
    | MERGE LINK-SAFE
    |--------------------------------------------------------------------------
    | FPDI importa le pagine come template e perde le annotazioni cliccabili.
    | Per preservare i link usiamo, in ordine:
    | 1. copia diretta se esiste un solo batch;
    | 2. qpdf;
    | 3. pdfunite.
    |
    | Se nessun merger è disponibile il job fallisce in JSON, senza corrompere
    | il PDF e senza ricadere su FPDI.
    */
    $mergeMethod = '';

    if (count($batchFiles) === 1) {
        if (!@copy($batchFiles[0], $outFile)) {
            throw new Exception('Impossibile copiare il batch singolo nel PDF finale.');
        }
        $mergeMethod = 'copy';
    } else {
        $runCommand = function(array $command, &$stdout = '', &$stderr = '') {
            if (!function_exists('proc_open')) {
                return 127;
            }

            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $process = @proc_open($command, $descriptors, $pipes, null, null, ['bypass_shell' => true]);

            if (!is_resource($process)) {
                return 127;
            }

            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            return proc_close($process);
        };

        $qpdfCandidates = ['/usr/bin/qpdf', '/usr/local/bin/qpdf', 'qpdf'];
        $pdfuniteCandidates = ['/usr/bin/pdfunite', '/usr/local/bin/pdfunite', 'pdfunite'];
        $mergedOk = false;
        $mergeErrors = [];

        foreach ($qpdfCandidates as $qpdf) {
            $command = array_merge([$qpdf, '--empty', '--pages'], $batchFiles, ['--', $outFile]);
            $stdout = $stderr = '';
            $exitCode = $runCommand($command, $stdout, $stderr);

            if ($exitCode === 0 && is_file($outFile) && filesize($outFile) > 0) {
                $mergedOk = true;
                $mergeMethod = 'qpdf';
                break;
            }

            @unlink($outFile);
            $mergeErrors[] = 'qpdf('.$qpdf.') exit '.$exitCode.': '.trim($stderr ?: $stdout);
        }

        if (!$mergedOk) {
            foreach ($pdfuniteCandidates as $pdfunite) {
                $command = array_merge([$pdfunite], $batchFiles, [$outFile]);
                $stdout = $stderr = '';
                $exitCode = $runCommand($command, $stdout, $stderr);

                if ($exitCode === 0 && is_file($outFile) && filesize($outFile) > 0) {
                    $mergedOk = true;
                    $mergeMethod = 'pdfunite';
                    break;
                }

                @unlink($outFile);
                $mergeErrors[] = 'pdfunite('.$pdfunite.') exit '.$exitCode.': '.trim($stderr ?: $stdout);
            }
        }

        if (!$mergedOk) {
            throw new Exception(
                'Nessun merger PDF link-safe disponibile sul server. ' .
                'Installare qpdf o poppler-utils/pdfunite. Dettagli: ' .
                implode(' | ', array_filter($mergeErrors))
            );
        }
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
