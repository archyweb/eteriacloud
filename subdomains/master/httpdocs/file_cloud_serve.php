<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);
if (!$app->isLogged()) {
    header("HTTP/1.1 401 Unauthorized");
    exit(0);
} else {    
    $imageId = $_GET['imageId'];
    $cloud = new StoreCloud();
    $filename = $cloud->getImageDetailFromDb($imageId,'file_name');  
    $filename_ext = pathinfo($filename, PATHINFO_EXTENSION);
    if($filename_ext==''){$filename=$filename.'.jpg';}
    file_put_contents($filename,$cloud->getOriginalImage($imageId));
    $download_file=$subdomain_root.'/'.$filename;
    if(!empty($filename)){
        // Check file is exists on given path.
        if(file_exists($download_file))
        {
          header('Content-Disposition: attachment; filename=' . $filename);  
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