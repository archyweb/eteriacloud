<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);

if (isset($_FILES)) {
    //print_r($_FILES);
    foreach($_FILES as $type=>$data){
        $file = $_FILES['video']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
       $cloud = new Images();
        $cloud->uploadVideo($data['tmp_name'],$filename,false);  
       
    }
}


echo '<form action="" method="post" enctype="multipart/form-data"><input name="video" type="file"><input type="submit" value="Carica"></form>';