<?php

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - RIGENERA ALLEGATI / WORKER BATCH
|--------------------------------------------------------------------------
|
| Versione corretta:
| - restituisce sempre JSON pulito;
| - non richiama sé stesso lato server;
| - processa un batch per chiamata;
| - usa progress come puntatore dell'ID successivo;
| - aggiorna archy_jobs;
| - evita output sporco prima/dopo json_encode().
|
*/

ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/regenerate-job-error.log');
set_time_limit(60);
ini_set('memory_limit', '1024M');

ob_start();

header('Content-Type: application/json; charset=utf-8');

/*
|--------------------------------------------------------------------------
| Risposta JSON sicura
|--------------------------------------------------------------------------
*/
function jsonResponse($data) {

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Path ambiente
|--------------------------------------------------------------------------
*/
$documentRoot = dirname(dirname(__DIR__));
$mainRoot = dirname(dirname(dirname($documentRoot)));

/*
|--------------------------------------------------------------------------
| Configurazione DB
|--------------------------------------------------------------------------
*/
$configFile = $documentRoot . '/config-local.php';

if (!file_exists($configFile)) {
    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'File config-local.php non trovato.'
    ]);
}

$config = require $configFile;

$db = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($db->connect_error) {
    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Errore connessione DB run-regenerate-jobs.php: ' . $db->connect_error
    ]);
}

$db->set_charset('utf8mb4');

/*
|--------------------------------------------------------------------------
| Lettura Job ID
|--------------------------------------------------------------------------
*/
$jobId = 0;

if (isset($argv[1])) {
    $jobId = (int) $argv[1];
} elseif (isset($_GET['job_id'])) {
    $jobId = (int) $_GET['job_id'];
}

if (!$jobId) {
    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Job ID mancante run-regenerate-jobs.php'
    ]);
}

/*
|--------------------------------------------------------------------------
| Recupero job
|--------------------------------------------------------------------------
*/
$jobQuery = $db->query("
    SELECT *
    FROM archy_jobs
    WHERE id = {$jobId}
    AND job_type = 'regenerate_attachments'
    LIMIT 1
");

if (!$jobQuery || $jobQuery->num_rows === 0) {
    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Job non trovato.'
    ]);
}

$job = $jobQuery->fetch_object();

/*
|--------------------------------------------------------------------------
| Se il job è già completato o fallito restituisco lo stato attuale
|--------------------------------------------------------------------------
*/
if ($job->status === 'completed') {
    jsonResponse([
        'success' => true,
        'status' => 'completed',
        'progress' => (int) $job->progress,
        'total' => (int) $job->total
    ]);
}

if ($job->status === 'failed') {
    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Job già fallito.',
        'progress' => (int) $job->progress,
        'total' => (int) $job->total
    ]);
}

/*
|--------------------------------------------------------------------------
| Payload
|--------------------------------------------------------------------------
*/
$payload = json_decode($job->payload, true);

if (!is_array($payload)) {
    $db->query("
        UPDATE archy_jobs
        SET status = 'failed',
            finished_at = NOW(),
            log = CONCAT(IFNULL(log,''), '\nPayload non valido')
        WHERE id = {$jobId}
    ");

    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Payload non valido.'
    ]);
}

$intent = isset($payload['intent']) ? (string) $payload['intent'] : '';
$batchSize = isset($payload['batch_size']) ? (int) $payload['batch_size'] : 1;

if ($batchSize <= 0 || $batchSize > 5) {
    $batchSize = 1;
}

if ($intent !== 'archy_art_catalog') {

    $safeIntent = $db->real_escape_string($intent);

    $db->query("
        UPDATE archy_jobs
        SET status = 'failed',
            finished_at = NOW(),
            log = CONCAT(IFNULL(log,''), '\nIntent non supportato: {$safeIntent}')
        WHERE id = {$jobId}
    ");

    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Intent non supportato: ' . $intent
    ]);
}

$allIds = isset($payload['ids']) && is_array($payload['ids'])
    ? array_values($payload['ids'])
    : [];

$allIds = array_values(array_filter(array_map('intval', $allIds)));

$total = count($allIds);
$processed = (int) $job->progress;
$errors = [];

if ($total <= 0) {

    $db->query("
        UPDATE archy_jobs
        SET status = 'failed',
            finished_at = NOW(),
            log = CONCAT(IFNULL(log,''), '\nNessun ID nel payload')
        WHERE id = {$jobId}
    ");

    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Nessun ID nel payload.'
    ]);
}

/*
|--------------------------------------------------------------------------
| Se progress è già arrivato al totale, chiudo
|--------------------------------------------------------------------------
*/
if ($processed >= $total) {

    $db->query("
        UPDATE archy_jobs
        SET status = 'completed',
            progress = {$total},
            total = {$total},
            finished_at = IFNULL(finished_at, NOW()),
            log = CONCAT(IFNULL(log,''), '\nRigenerazione completata')
        WHERE id = {$jobId}
    ");

    jsonResponse([
        'success' => true,
        'status' => 'completed',
        'progress' => $total,
        'total' => $total
    ]);
}

/*
|--------------------------------------------------------------------------
| Stato processing
|--------------------------------------------------------------------------
*/
$db->query("
    UPDATE archy_jobs
    SET status = 'processing',
        started_at = IFNULL(started_at, NOW()),
        total = {$total}
    WHERE id = {$jobId}
");

/*
|--------------------------------------------------------------------------
| Caricamento ambiente gestionale
|--------------------------------------------------------------------------
*/
$subdomain_root = $documentRoot;
$main_root = $mainRoot;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = $documentRoot;
}

if (empty($_SERVER['HTTP_HOST']) && !empty($config['subdomain_url'])) {
    $_SERVER['HTTP_HOST'] = parse_url($config['subdomain_url'], PHP_URL_HOST);
}

try {

    require_once $mainRoot . '/private/_common/classes/load_classes.php';

    /*
    | Dopo il caricamento classi abbassiamo il livello errori a fatal/parse:
    | eventuali warning/deprecated non devono sporcare il JSON.
    */
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', 0);

    $app = new App($subdomain_root);

    require_once $mainRoot . '/private/_common/intents/archy_art_catalog.php';

    $app->init();
    $GLOBALS['app'] = $app;

    $managePdf = new ManagePdf();

    /*
    |--------------------------------------------------------------------------
    | Batch corrente
    |--------------------------------------------------------------------------
    | Usa progress come indice di partenza.
    */
    $ids = array_slice($allIds, $processed, $batchSize);

    foreach ($ids as $recordId) {

        $recordId = (int) $recordId;

        if ($recordId <= 0) {
            $processed++;
            continue;
        }

        try {

            $managePdf->init_pdf(
                $recordId,
                'standard',
                ['data_intent' => 'archy_art_catalog']
            );

        } catch (Throwable $e) {

            $errors[] = 'ID ' . $recordId . ': ' . $e->getMessage();
        }

        $processed++;

        $db->query("
            UPDATE archy_jobs
            SET progress = {$processed},
                total = {$total},
                log = CONCAT(IFNULL(log,''), '\nRigenerata scheda ID {$recordId}')
            WHERE id = {$jobId}
        ");

        /*
        | Piccola pausa per non saturare PHP-FPM.
        */
        usleep(200000);
    }

} catch (Throwable $e) {

    $msg = $db->real_escape_string($e->getMessage());

    $db->query("
        UPDATE archy_jobs
        SET status = 'failed',
            finished_at = NOW(),
            log = CONCAT(IFNULL(log,''), '\nErrore fatale worker: {$msg}')
        WHERE id = {$jobId}
    ");

    jsonResponse([
        'success' => false,
        'status' => 'failed',
        'message' => 'Errore fatale worker: ' . $e->getMessage(),
        'progress' => $processed,
        'total' => $total
    ]);
}

/*
|--------------------------------------------------------------------------
| Chiusura o stato intermedio
|--------------------------------------------------------------------------
*/
if ($processed >= $total) {

    $errorLog = '';

    if (!empty($errors)) {
        $errorLog = $db->real_escape_string(
            "\nErrori:\n" . implode("\n", array_slice($errors, 0, 100))
        );
    }

    $db->query("
        UPDATE archy_jobs
        SET status = 'completed',
            progress = {$processed},
            total = {$total},
            finished_at = NOW(),
            log = CONCAT(IFNULL(log,''), '{$errorLog}', '\nRigenerazione completata')
        WHERE id = {$jobId}
    ");

    jsonResponse([
        'success' => true,
        'status' => 'completed',
        'progress' => $processed,
        'total' => $total
    ]);
}

/*
|--------------------------------------------------------------------------
| Stato processing: il browser richiamerà il batch successivo
|--------------------------------------------------------------------------
*/
$db->query("
    UPDATE archy_jobs
    SET status = 'processing',
        progress = {$processed},
        total = {$total}
    WHERE id = {$jobId}
");

jsonResponse([
    'success' => true,
    'status' => 'processing',
    'progress' => $processed,
    'total' => $total
]);
