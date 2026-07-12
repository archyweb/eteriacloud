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
| ACCORPAMENTO PDF
|--------------------------------------------------------------------------
*/

$outputName = 'schede_catalogo_filtrate_' . date('Ymd_His') . '.pdf';
$outputFile = $outputDir . $outputName;

try {

    $merged = new Fpdi();

    foreach ($pdfs as $file) {

        $pageCount = $merged->setSourceFile($file);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $template = $merged->importPage($pageNo);
            $size = $merged->getTemplateSize($template);

            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

            $merged->AddPage($orientation, [$size['width'], $size['height']]);
            $merged->useTemplate($template);
        }
    }

    $merged->Output($outputFile, 'F');

} catch (Throwable $e) {

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
