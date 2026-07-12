<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);


function getRemoteFileSize($url){
        $headers = get_headers($url, 1);

        if (isset($headers['Content-Length'])) {
            return (int)$headers['Content-Length'];
        }

        return null;
    }

$db=$app->get_db();
$columns=$db->query("select id,file_cloud_id from archy_files where file_location='cloudImages'"); 
while($domain=mysqli_fetch_object($columns)):
    $size = getRemoteFileSize("https://imagedelivery.net/9SuSEzNRJh_MWD3dytly4A/{$domain->file_cloud_id}/public");

    $db->query("update archy_files set file_media_size='{$size}' where id='{$domain->id}/'"); 

endwhile;
    
