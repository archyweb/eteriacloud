<?php
//
// FILESERVE.PHP
// 25-nov-2020
// Erogazione protetta file
//


// --- Autenticazione
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);
if (!$app->isLogged()) {
    header("HTTP/1.1 401 Unauthorized");
    exit(0);
} else {
    // header("x-eteria: fileserve.php");
    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    
    // --- Definisco accesso al Bucket su S3, per accedere a questo file:
    //     https://pippo.s3-eu-west-1.amazonaws.com/ciccio.pdf
    //     con la chiamata:
    //     https://dev.eteriacloud.it/fileserve.php?f=ciccio.pdf
    //define('BUCKET', 'pippo');
    //define('S3', 's3-eu-west-1.amazonaws.com');
    //$fileToRead=filter_input(INPUT_GET, 'f', FILTER_SANITIZE_ENCODED);
    //$filePath = 'https://'.BUCKET.'.'.S3."/".$fileToRead;

    /*
    $fileA=explode('/',$fn);
    $file=end($fileA);
    $file=filter_input(INPUT_GET, 'f', FILTER_SANITIZE_ENCODED);
    $fileA=array_pop($fileA);
    $fn=implode($fileA).$file;
    */

    //$fileToRead=filter_input(INPUT_GET, 'f', FILTER_SANITIZE_ENCODED);
    $fileToRead=filter_input(INPUT_GET, 'f');
    //error_log("@@@@@@@@@@ fileserve f=".$fileToRead);

    $fileA=explode('/',$fileToRead);
    $downloadFileName=end($fileA);

    //$fileToRead='receipts/2020/1/RF0001-2020_NBM.pdf';

    $filePath = $subdomain_root."/assets/documents/".$fileToRead;

    if(file_exists($filePath)) {
        $file = fopen($filePath, "r");
        $download_rate = 20.5;

        header('Content-Type: '.mime_content_type($filePath));
        header("Content-disposition: inline; filename=\"" . $downloadFileName . "\"");
        /*
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . $downloadFileName . "\"");
        */

        while(!feof($file))
        {
            // send the current file part to the browser
            echo fread($file, round($download_rate * 1024));
            flush();
            usleep(5000);
        }
        fclose($file);
    }

}
