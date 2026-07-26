<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);
if (!$app->isLogged()) {
    header("HTTP/1.1 401 Unauthorized");
    exit(0);
} else {    
    $imageId = isset($_GET['imageId']) ? trim($_GET['imageId']) : '';
    if($imageId == ''){
        header("HTTP/1.1 400 Bad Request");
        exit('Missing imageId');
    }

    $cloud = new StoreCloud();
    $filename = $cloud->getImageDetailFromDb($imageId,'file_name');
    if($filename == ''){
        $filename = $imageId.'.jpg';
    }

    $filename = basename(str_replace('\\', '/', $filename));
    $filename = $cloud->normalizeFileName($filename);
    $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
    if($filename_ext==''){$filename=$filename.'.jpg';}

    $originalImage = $cloud->getOriginalImage($imageId);
    if($originalImage === false || $originalImage === ''){
        header("HTTP/1.1 404 Not Found");
        exit('Original image not found');
    }

    $tmp_dir = $subdomain_root.'/tmp';
    if(!is_dir($tmp_dir) && !@mkdir($tmp_dir, 0775, true)){
        header("HTTP/1.1 500 Internal Server Error");
        exit('Temporary directory not writable');
    }

    $download_file = $tmp_dir.'/'.uniqid('cloud_original_', true).'_'.$filename;
    if(@file_put_contents($download_file,$originalImage) === false){
        header("HTTP/1.1 500 Internal Server Error");
        exit('Unable to prepare original image');
    }

    if(!empty($filename)){
        // Check file is exists on given path.
        if(file_exists($download_file))
        {
          header('Content-Type: application/octet-stream');
          header('Content-Length: ' . filesize($download_file));
          header('Content-Disposition: attachment; filename="' . $filename . '"');
          readfile($download_file); 
          unlink($download_file);
          exit;
        }
        else
        {
          echo 'File does not exists on given path';
        }
     }     
}
