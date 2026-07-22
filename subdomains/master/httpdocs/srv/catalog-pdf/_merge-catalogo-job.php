<?php

/*
|--------------------------------------------------------------------------
| MERGE CATALOGO PDF - /srv/catalog-pdf/merge-catalog.php
|--------------------------------------------------------------------------
|
| Questo file genera il PDF accorpato delle schede catalogo filtrate.
|
| NOTA IMPORTANTE
| -------------------------------------------------------------------------
| Questo script viene chiamato da:
|
|     /srv/catalog-pdf/merge-catalog.php
|
| quindi NON si trova necessariamente dentro la root pubblica del
| sottodominio.
|
| Per questo motivo NON bisogna usare __DIR__ per cercare:
|
|     assets/documents/scheda_catalogo/
|
| ma bisogna risalire alla DOCUMENT_ROOT del sottodominio corrente.
|
| Correzioni introdotte:
| - legge correttamente ids=2455,2456;
| - cerca i PDF nella cartella reale del sottodominio;
| - supporta più naming delle schede catalogo;
| - accorpa tutti i PDF trovati, non solo il primo/ultimo;
| - restituisce direttamente un PDF valido al fetch JavaScript;
| - scrive un log diagnostico.
|
*/

ini_set('display_errors', 0);
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| HELPER RISPOSTA ERRORE
|--------------------------------------------------------------------------
|
| Il JavaScript intercetta una risposta non PDF e mostra il testo.
|
*/

function merge_catalog_error($message, $httpCode = 500) {
    http_response_code($httpCode);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit;
}

/*
|--------------------------------------------------------------------------
| LETTURA ID RICEVUTI
|--------------------------------------------------------------------------
|
| Accetta sia GET che POST.
| Manteniamo anche "id" come fallback perché il JavaScript invia entrambi:
|
|     ids=2455,2456&id=2455,2456
|
*/

$rawIds = '';

if (!empty($_REQUEST['ids'])) {
    $rawIds = $_REQUEST['ids'];
} elseif (!empty($_REQUEST['id'])) {
    $rawIds = $_REQUEST['id'];
}

$ids = array_filter(array_map('intval', explode(',', (string)$rawIds)));
$ids = array_values(array_unique($ids));

if (empty($ids)) {
    merge_catalog_error('Nessun ID ricevuto.', 400);
}

/*
|--------------------------------------------------------------------------
| RISOLUZIONE ROOT PUBBLICA SOTTODOMINIO
|--------------------------------------------------------------------------
|
| In questa installazione lo script è sotto /srv/catalog-pdf/, mentre
| i PDF delle schede catalogo sono sotto:
|
|     DOCUMENT_ROOT/assets/documents/scheda_catalogo/
|
| Usiamo quindi DOCUMENT_ROOT come percorso principale.
|
*/

$possibleDocumentRoots = [];

// Root pubblica reale della richiesta corrente.
if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $possibleDocumentRoots[] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
}

// Fallback specifici per l'ambiente Archy Media / staging.
$possibleDocumentRoots[] = '/var/www/vhosts/archymedia.com/subdomains/archymedia/httpdocs';
$possibleDocumentRoots[] = '/var/www/vhosts/archymedia.com/httpdocs';
$possibleDocumentRoots[] = dirname(__DIR__) . '/httpdocs';
$possibleDocumentRoots[] = dirname(__DIR__);

$documentRoot = null;
$catalogFolder = null;

foreach ($possibleDocumentRoots as $candidateRoot) {
    $candidateFolder = rtrim($candidateRoot, '/') . '/assets/documents/scheda_catalogo/';

    if (is_dir($candidateFolder)) {
        $documentRoot = rtrim($candidateRoot, '/');
        $catalogFolder = $candidateFolder;
        break;
    }
}

if (!$documentRoot || !$catalogFolder) {
    merge_catalog_error(
        "Cartella schede catalogo non trovata.\n" .
        "Percorsi controllati:\n- " .
        implode("\n- ", array_map(function($r){
            return rtrim($r, '/') . '/assets/documents/scheda_catalogo/';
        }, $possibleDocumentRoots))
    );
}

/*
|--------------------------------------------------------------------------
| RISOLUZIONE LIBRERIE TCPDF / FPDI
|--------------------------------------------------------------------------
*/

$possibleTcpdfPaths = [
    '/var/www/vhosts/archymedia.com/private/_common/classes/tcpdf/tcpdf.php',
    '/var/www/vhosts/archyart.it/private/_common/classes/tcpdf/tcpdf.php',
    dirname($documentRoot, 2) . '/private/_common/classes/tcpdf/tcpdf.php',
    dirname($documentRoot, 1) . '/private/_common/classes/tcpdf/tcpdf.php',
];

$possibleFpdiPaths = [
    '/var/www/vhosts/archymedia.com/private/_common/classes/fpdi/src/autoload.php',
    '/var/www/vhosts/archyart.it/private/_common/classes/fpdi/src/autoload.php',
    dirname($documentRoot, 2) . '/private/_common/classes/fpdi/src/autoload.php',
    dirname($documentRoot, 1) . '/private/_common/classes/fpdi/src/autoload.php',
];

$tcpdfPath = null;
$fpdiPath = null;

foreach ($possibleTcpdfPaths as $path) {
    if (file_exists($path)) {
        $tcpdfPath = $path;
        break;
    }
}

foreach ($possibleFpdiPaths as $path) {
    if (file_exists($path)) {
        $fpdiPath = $path;
        break;
    }
}

if (!$tcpdfPath) {
    merge_catalog_error('TCPDF non trovato.');
}

if (!$fpdiPath) {
    merge_catalog_error('FPDI non trovato.');
}

require_once $tcpdfPath;
require_once $fpdiPath;

use setasign\Fpdi\Tcpdf\Fpdi;

/*
|--------------------------------------------------------------------------
| CARTELLA OUTPUT E LOG
|--------------------------------------------------------------------------
*/

$outputDir = $documentRoot . '/assets/documents/schede_catalogo_accorpate/';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$logFile = $outputDir . 'ultimo_merge_log.txt';

/*
|--------------------------------------------------------------------------
| HELPER LOG
|--------------------------------------------------------------------------
*/

function merge_catalog_write_log($logFile, $lines) {
    @file_put_contents($logFile, implode("\n", $lines));
}


/*
|--------------------------------------------------------------------------
| HELPER AVANZAMENTO JOB CATALOGO PDF
|--------------------------------------------------------------------------
|
| Se lo script viene chiamato dal worker con ?job_id=..., aggiorna
| archy_jobs.progress durante l'accorpamento. In questo modo la griglia
| può mostrare un contatore reale: Catalogo PDF X / Y.
|
| La funzione apre una connessione leggera al DB solo quando serve e
| non interrompe mai il merge se l'update progress fallisce.
|
*/
function catalog_pdf_update_job_progress($jobId, $progress, $total = null, $status = null, $resultFile = null, $appendLog = null) {

    $jobId = (int)$jobId;

    if ($jobId <= 0) {
        return;
    }

    static $db = null;

    if ($db === null) {

        $configPath = dirname(dirname(__DIR__)) . '/config-local.php';

        if (!file_exists($configPath)) {
            return;
        }

        $config = require $configPath;

        if (!isset($config['db'])) {
            return;
        }

        $db = @new mysqli(
            $config['db']['host'],
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['name']
        );

        if ($db->connect_error) {
            $db = null;
            return;
        }
    }

    $sets = [];
    $sets[] = "progress = " . (int)$progress;

    if ($total !== null) {
        $sets[] = "total = " . (int)$total;
    }

    if ($status !== null) {
        $sets[] = "status = '" . $db->real_escape_string($status) . "'";
    }

    if ($resultFile !== null) {
        $sets[] = "result_file = '" . $db->real_escape_string($resultFile) . "'";
    }

    if ($appendLog !== null && $appendLog !== '') {
        $sets[] = "log = CONCAT(IFNULL(log, ''), '\\n" . $db->real_escape_string($appendLog) . "')";
    }

    @ $db->query("UPDATE archy_jobs SET " . implode(', ', $sets) . " WHERE id = " . $jobId);
}

/*
|--------------------------------------------------------------------------
| RICERCA PDF PER RECORD
|--------------------------------------------------------------------------
|
| La funzione è volutamente elastica perché nel tempo possono essere
| esistiti naming diversi.
|
| Pattern gestiti:
| - scheda_catalogo_*_ID.pdf
| - scheda_catalogo_ID.pdf
| - ID_scheda_catalogo.pdf
| - qualsiasi PDF contenente "scheda_catalogo" e l'ID come token separato
|
| Vengono esclusi:
| - schede generiche;
| - PDF di archiviazione;
| - NON esclude scheda_catalogo_0_ID.pdf perché è una scheda valida
|   quando il numero di archiviazione non è compilato.
|
*/

function find_catalog_pdf_for_record($catalogFolder, $id) {

    /*
    |--------------------------------------------------------------------------
    | RICERCA PDF PRINCIPALE PER RECORD - NAMING DINAMICO
    |--------------------------------------------------------------------------
    |
    | Dopo l'introduzione dei nomi file personalizzati, la scheda catalogo
    | principale può chiamarsi in diversi modi:
    |
    | - scheda_catalogo_0_2457.pdf
    | - scheda_catalogo_ABC_2457.pdf
    | - LV-2009-019_Senza-Titolo_2457.pdf
    |
    | La scheda generica / uso interno invece può chiamarsi:
    |
    | - scheda_catalogo_archiviazione_0_2457.pdf
    | - scheda_generica_LV-2009-019_2457.pdf
    |
    | Regola corretta per il catalogo accorpato:
    | - prendere SOLO la scheda principale;
    | - escludere scheda_generica / archiviazione;
    | - scegliere il PDF più recente tra quelli che terminano con _ID.pdf;
    | - mantenere compatibilità con i vecchi nomi.
    |
    */

    $id = (int)$id;
    $allFiles = glob(rtrim($catalogFolder, '/') . '/*.pdf');
    $candidates = [];

    if (empty($allFiles)) {
        return null;
    }

    foreach ($allFiles as $file) {

        if (!is_file($file)) {
            continue;
        }

        $basename = basename($file);
        $basenameLower = strtolower($basename);

        /*
        | L'ID record deve essere alla fine del nome, prima di .pdf.
        */
        if (!preg_match('/(^|[_\-])' . $id . '\.pdf$/i', $basename)) {
            continue;
        }

        /*
        | Esclude la scheda generica / archiviazione: nel catalogo accorpato
        | deve finire solo la scheda catalogo principale.
        */
        if (
            strpos($basenameLower, 'scheda_generica_') === 0 ||
            strpos($basenameLower, 'archiviazione') !== false ||
            strpos($basenameLower, 'archive') !== false ||
            strpos($basenameLower, 'generica') !== false
        ) {
            continue;
        }

        $candidates[] = $file;
    }

    $candidates = array_values(array_unique($candidates));

    if (empty($candidates)) {
        return null;
    }

    /*
    | Se per lo stesso record esistono vecchi PDF e nuovi PDF dinamici,
    | usa quello generato più recentemente.
    */
    usort($candidates, function($a, $b){
        return filemtime($b) - filemtime($a);
    });

    return $candidates[0];
}

/*
|--------------------------------------------------------------------------
| RECUPERO PDF DA ACCORPARE
|--------------------------------------------------------------------------
*/

$pdfs = [];
$missing = [];

foreach ($ids as $id) {

    $pdfFile = find_catalog_pdf_for_record($catalogFolder, $id);

    if ($pdfFile) {
        $pdfs[] = $pdfFile;
    } else {
        $missing[] = $id;
    }
}

$pdfs = array_values(array_unique($pdfs));

$log = [];
$log[] = 'Data: ' . date('Y-m-d H:i:s');
$log[] = 'Script: ' . __FILE__;
$log[] = 'DOCUMENT_ROOT: ' . $documentRoot;
$log[] = 'Cartella schede: ' . $catalogFolder;
$log[] = 'ID ricevuti: ' . implode(', ', $ids);
$log[] = 'PDF trovati: ' . count($pdfs);
$log[] = 'File usati:';

foreach ($pdfs as $p) {
    $log[] = '- ' . basename($p);
}

if (!empty($missing)) {
    $log[] = 'ID senza PDF trovato: ' . implode(', ', $missing);
}

merge_catalog_write_log($logFile, $log);

if (empty($pdfs)) {
    merge_catalog_error(
        'Nessun PDF scheda catalogo trovato per gli ID ricevuti: ' .
        implode(', ', $ids)
    );
}

/*
|--------------------------------------------------------------------------
| PULIZIA VECCHI CATALOGHI ACCORPATI
|--------------------------------------------------------------------------
|
| Pulisce solo i PDF accorpati, non le schede singole.
|
*/

foreach (glob($outputDir . 'schede_catalogo_filtrate_*.pdf') as $oldPdf) {
    if (is_file($oldPdf)) {
        @unlink($oldPdf);
    }
}

/*
|--------------------------------------------------------------------------
| ACCORPAMENTO PDF A BATCH CON LOG DIAGNOSTICO
|--------------------------------------------------------------------------
|
| Per cataloghi grandi non accorpiamo tutti i PDF in una sola istanza FPDI,
| perché con centinaia/migliaia di schede si rischia saturazione memoria.
|
| Flusso:
| 1. divide i PDF in batch da 50;
| 2. genera file temporanei tmp_batch_N.pdf;
| 3. accorpa i batch temporanei nel PDF finale;
| 4. elimina i temporanei;
| 5. scrive avanzamento in ultimo_merge_log.txt.
|
*/

$outputName = 'schede_catalogo_filtrate_' . date('Ymd_His') . '.pdf';
$outputFile = $outputDir . $outputName;

$catalogJobId = !empty($_GET['job_id']) ? (int) $_GET['job_id'] : 0;
$catalogProgress = 0;
$catalogTotal = count($ids);

if ($catalogJobId > 0) {
    catalog_pdf_update_job_progress(
        $catalogJobId,
        0,
        $catalogTotal,
        'processing',
        null,
        'Avvio generazione Catalogo PDF'
    );
}

/*
|--------------------------------------------------------------------------
| CONNESSIONE DB PER JOB ASINCRONO
|--------------------------------------------------------------------------
|
| Serve solo per aggiornare progress/total del catalogo PDF
| mentre i batch vengono elaborati.
|
*/

$db = null;

if (!empty($_GET['job_id'])) {

    $config = require dirname(dirname(__DIR__)) . '/config-local.php';

    $db = new mysqli(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name']
    );
}

try {

    $batchSize = 50;
    $chunks = array_chunk($pdfs, $batchSize);
    $tempFiles = [];

    @file_put_contents(
        $logFile,
        "\nAvvio accorpamento batch. Totale PDF: " . count($pdfs) .
        " | Batch size: " . $batchSize .
        " | Numero batch: " . count($chunks),
        FILE_APPEND
    );

    foreach ($chunks as $chunkIndex => $chunkFiles) {
		
		/*
|--------------------------------------------------------------------------
| AGGIORNA PROGRESS JOB
|--------------------------------------------------------------------------
*/






        $batchNumber = $chunkIndex + 1;
        $tempFile = $outputDir . 'tmp_batch_' . $batchNumber . '_' . date('Ymd_His') . '.pdf';

        @file_put_contents(
            $logFile,
            "\nAvvio batch " . $batchNumber . " di " . count($chunks) .
            " | PDF nel batch: " . count($chunkFiles),
            FILE_APPEND
        );

        $batchPdf = new Fpdi();

        foreach ($chunkFiles as $file) {

            @file_put_contents(
                $logFile,
                "\n  - Batch " . $batchNumber . " importa: " . basename($file),
                FILE_APPEND
            );

            $pageCount = $batchPdf->setSourceFile($file);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $template = $batchPdf->importPage($pageNo);
                $size = $batchPdf->getTemplateSize($template);

                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                $batchPdf->AddPage(
                    $orientation,
                    [$size['width'], $size['height']]
                );

                $batchPdf->useTemplate($template);
            }

            /*
            | Aggiorna il contatore dopo ogni PDF sorgente importato.
            | Il totale resta quello degli ID richiesti, così il bottone mostra
            | lo stesso numero confermato dall'utente in griglia.
            */
            if ($catalogJobId > 0) {
                $catalogProgress++;
                catalog_pdf_update_job_progress(
                    $catalogJobId,
                    $catalogProgress,
                    $catalogTotal,
                    'processing'
                );
            }
        }

        $batchPdf->Output($tempFile, 'F');
        unset($batchPdf);

        if (!file_exists($tempFile) || filesize($tempFile) <= 0) {
            throw new Exception('Batch non creato o vuoto: ' . basename($tempFile));
        }

        $tempFiles[] = $tempFile;

        @file_put_contents(
            $logFile,
            "\nCreato batch " . $batchNumber . ": " . basename($tempFile) .
            " | Dimensione: " . filesize($tempFile) . " byte",
            FILE_APPEND
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MERGE FINALE DEI BATCH
    |--------------------------------------------------------------------------
    */

    @file_put_contents(
        $logFile,
        "\nAvvio merge finale dei batch. Totale batch: " . count($tempFiles),
        FILE_APPEND
    );

    $merged = new Fpdi();

    foreach ($tempFiles as $tempIndex => $tempFile) {

        @file_put_contents(
            $logFile,
            "\nMerge finale batch " . ($tempIndex + 1) . ": " . basename($tempFile),
            FILE_APPEND
        );

        $pageCount = $merged->setSourceFile($tempFile);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $template = $merged->importPage($pageNo);
            $size = $merged->getTemplateSize($template);

            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $merged->AddPage(
                $orientation,
                [$size['width'], $size['height']]
            );

            $merged->useTemplate($template);
        }
    }

    $merged->Output($outputFile, 'F');
    unset($merged);

    if (!file_exists($outputFile) || filesize($outputFile) <= 0) {
        throw new Exception('PDF finale non creato o vuoto.');
    }

    @file_put_contents(
        $logFile,
        "\nPDF finale creato: " . basename($outputFile) .
        " | Dimensione: " . filesize($outputFile) . " byte",
        FILE_APPEND
    );

    /*
    |--------------------------------------------------------------------------
    | PULIZIA FILE TEMPORANEI
    |--------------------------------------------------------------------------
    */

    foreach ($tempFiles as $tempFile) {
        if (is_file($tempFile)) {
            @unlink($tempFile);
        }
    }

    @file_put_contents(
        $logFile,
        "\nPulizia temporanei completata.",
        FILE_APPEND
    );

} catch (Throwable $e) {

    @file_put_contents(
        $logFile,
        "\nERRORE ACCORPAMENTO: " . $e->getMessage(),
        FILE_APPEND
    );

    merge_catalog_error(
        "Errore durante l'accorpamento PDF:\n" .
        $e->getMessage()
    );
}

if (!file_exists($outputFile) || filesize($outputFile) <= 0) {
    merge_catalog_error('La generazione non ha prodotto un PDF valido.');
}

/*
|--------------------------------------------------------------------------
| OUTPUT DIRETTO PDF
|--------------------------------------------------------------------------
|
| Il JavaScript usa fetch() e si aspetta un blob PDF.
| Per questo NON facciamo redirect, ma restituiamo direttamente il file.
|
*/

/*
|--------------------------------------------------------------------------
| JOB MODE
|--------------------------------------------------------------------------
|
| Se il merge viene eseguito dal worker jobs,
| aggiorniamo il job invece di restituire il PDF al browser.
|
*/

if (!empty($_GET['job_id'])) {

    $jobId = (int)$_GET['job_id'];

    $publicFile =
        '/assets/documents/schede_catalogo_accorpate/' .
        $outputName;

    catalog_pdf_update_job_progress(
        $jobId,
        $catalogTotal,
        $catalogTotal,
        'completed',
        $publicFile,
        'Catalogo PDF generato correttamente'
    );

    echo "JOB COMPLETATO";
    exit;
}

/*
|--------------------------------------------------------------------------
| MODALITÀ STANDARD
|--------------------------------------------------------------------------
*/

if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="catalogo_accorpato.pdf"');
header('Content-Length: ' . filesize($outputFile));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($outputFile);
exit;