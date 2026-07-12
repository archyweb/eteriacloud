<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

$documentRoot = dirname(dirname(__DIR__));

$pdfDir = $documentRoot . '/assets/documents/schede_catalogo_accorpate/';

$files = glob($pdfDir . 'schede_catalogo_filtrate_*.pdf');

if (!$files) {
    http_response_code(404);
    echo 'Nessun catalogo PDF trovato.';
    exit;
}

usort($files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

$latestFile = $files[0];

$publicUrl = '/assets/documents/schede_catalogo_accorpate/' . basename($latestFile);

header('Location: ' . $publicUrl . '?t=' . time());
exit;