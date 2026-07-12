<?php
    //ini_set('memory_limit', '512M');

    // Disabilita timeout
    set_time_limit(0);
    $main_root = dirname(dirname(dirname(dirname(__FILE__))));
    $subdomain_root = dirname(__FILE__);
    require_once $main_root . '/private/_common/classes/load_classes.php';
    $config=require $subdomain_root.'/config-local.php';
    $app = new App($subdomain_root);

    $mysqli = new mysqli('localhost', $config['db']['user'], $config['db']['pass'], $config['db']['name']);
    if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
             exit();
    }
    
    function formatBytes($size, $precision = 0){
        $unit = ['Byte','KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];

        for($i = 0; $size >= 1024 && $i < count($unit)-1; $i++){
            $size /= 1024;
        }
        return round($size, $precision);
        //return round($size, $precision).' '.$unit[$i];
    }

    function getStoredFilePaths($value){
        if(!is_string($value) || trim($value) == ''){
            return [];
        }

        $unserialized = @unserialize($value);
        if($unserialized !== false || $value === 'b:0;'){
            return is_array($unserialized) ? $unserialized : [$unserialized];
        }

        $decoded = json_decode($value, true);
        if(json_last_error() === JSON_ERROR_NONE){
            return is_array($decoded) ? $decoded : [$decoded];
        }

        return [$value];
    }


    function isStoredFileCollection($value){
        if(!is_string($value) || trim($value) == ''){
            return false;
        }

        $unserialized = @unserialize($value);
        if($unserialized !== false || $value === 'b:0;'){
            return is_array($unserialized);
        }

        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE && is_array($decoded);
    }
    function buildUploadFileFromServer($absolute_path, $file_name){
        $mime_type = 'application/octet-stream';
        if(function_exists('mime_content_type')){
            $detected_mime = mime_content_type($absolute_path);
            if($detected_mime){
                $mime_type = $detected_mime;
            }
        }

        return [
            'name' => $file_name,
            'type' => $mime_type,
            'tmp_name' => $absolute_path,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($absolute_path),
        ];
    }

    function addVerifyLine($verify_log_path, $field_name, $id, $file_name, $problem){
        $line = $field_name.' - Numero opera '.$id.' - '.$file_name.' - '.$problem.PHP_EOL;
        file_put_contents($verify_log_path, $line, FILE_APPEND);
    }
    
   /* function compress($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        }
        elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        }
        elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        }
        elseif ($info['mime'] == 'image/bmp') {
            $image = imagecreatefrombmp($source);
        }
         imagejpeg($image, $destination, $quality);

        return $destination;
    }*/
    
    /**
 * Optimize image image
 * 
 * https://developers.google.com/speed/docs/insights/OptimizeImages
 * -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace sRGB
 *
 * @access public
 * @param string $filePath Path of the file
 * @return string Raw image result from the process
 */
function optimize($filePath,$file_name) 
{
    /**
     * Compress image
     */
    $imagick        = new Imagick($filePath);

    //$rawImage = file_get_contents($filePath);

    //$imagick->readImageBlob($rawImage);
    //$imagick->stripImage();

    // Define image
    $width      = $imagick->getImageWidth();
    $height     = $imagick->getImageHeight();

    // Compress image
    $imagick->setImageCompressionQuality(85);

    $image_types = getimagesize($filePath);

    // Get thumbnail image
    $imagick->thumbnailImage($width, $height);

    // Set image as based its own type
    if ($image_types[2] === IMAGETYPE_JPEG)
    {
        $imagick->setImageFormat('jpeg');

        $imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));

        $profiles = $imagick->getImageProfiles("icc", true);

        $imagick->stripImage();

        if(!empty($profiles)) {
            $imagick->profileImage('icc', $profiles['icc']);
        }

        $imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
        $imagick->setColorspace(Imagick::COLORSPACE_SRGB);
    }
    else if ($image_types[2] === IMAGETYPE_PNG) 
    {
        $imagick->setImageFormat('png');
    }
    else if ($image_types[2] === IMAGETYPE_GIF) 
    {
        $imagick->setImageFormat('gif');
    }

    // Get image raw data
    $rawData = $imagick->getImageBlob();
    $temp_path=dirname(__FILE__).'/assets/storage/temp/';
    if(!is_dir($temp_path)):
            mkdir($temp_path,0777);
    endif;
    $imagick->writeImage($temp_path.$file_name); 
   
    // Destroy image from memory
    $imagick->destroy();

    return $rawData;
}
/*

DROP TABLE `archy_stats`;

CREATE TABLE `archy_stats` (
  `id` bigint(11) NOT NULL,
  `stats_total_cloud_img` bigint(20) NOT NULL,
  `stats_weight_cloud_img` bigint(20) NOT NULL,
  `stats_total_local_img` bigint(20) NOT NULL,
  `stats_weight_local_img` bigint(20) NOT NULL,
  `stats_total_video` bigint(20) NOT NULL,
  `stats_total_docs` bigint(20) NOT NULL,
  `stats_weight_docs` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
 
ALTER TABLE `archy_stats` ADD PRIMARY KEY(`id`);
 * 
INSERT INTO `archy_stats` VALUES (1, 0, 0, 0, 0, 0, 0, 0, current_timestamp(), current_timestamp());
 */    


    //$fields = array('foto_ufficiale','foto_ufficiale_secondaria','documentazione_fotografica');
    $fields = array('foto_ufficiale','foto_ufficiale_secondaria','documentazione_fotografica');
    $fields_m = array('file_fotografico','condition_report','documentazione_grafica','certificato_assicurativo','certificato_archiviazione');
    //$mysqli->query("INSERT INTO `archy_stats` VALUES (1, 0, 0, 0, 0, 0, 0, 0,0, current_timestamp(), current_timestamp())"); 
    if (!is_dir('assets/storage')) {
        mkdir('assets/storage', 0777, true);
    }
    $verify_log_path = dirname(__FILE__).'/assets/storage/update_images_to_cloudflare_verificare.txt';
    file_put_contents($verify_log_path, 'Nome campo - Numero opera - Nome file - PROBLEMA'.PHP_EOL);
    $q = $mysqli->query("select * from archy_art_catalog");
   /* $array_images = array(
        
        'foto_ufficiale' => '/assets/documents/138_85992_2_d.tif',
        'foto_ufficiale_secondaria' => '/assets/documents/GT 1963 0002 retro.jpg',
      
    );*/
    
    $whitelist_type = array('jpeg', 'png','gif','bmp','jpg','webp','JPG','JPEG','HEIC');
    
    $cloudFlareStorage = new StoreR2Adapter();
    $storeFileRepository = new StoreFileRepository();
    $statsCloudStorageService = new StatsCloudStorageService();
    
    
    while ($rows = mysqli_fetch_object($q)){
        $id=$rows->id;
    //foreach($array_images as $key => $value){
        
        for($i=0;$i<count($fields);$i++){
            $stored_value = $rows->{$fields[$i]};
            if(strpos((string)$stored_value, 'assets') === false){
                continue;
            }

            echo 'TROVATO IN <strong>'.$fields[$i].'</strong> nell\'opera numero <strong>'.$id.'</strong> un file :'.$stored_value.'<br>';

            $stored_paths = getStoredFilePaths($stored_value);
            $is_multiple_value = isStoredFileCollection($stored_value);
            $to_store = [];
            $has_uploaded_files = false;

            foreach($stored_paths as $full_path){
                $isAllowedImage = false;
                unset($imageId, $newfilename, $uploaded_file_size, $uploaded_file_type);

                if(!is_string($full_path) || trim($full_path) == ''){
                    continue;
                }

                if(strpos($full_path, 'assets') === false){
                    $to_store[] = $full_path;
                    continue;
                }

                $file_name_parts=explode('/', str_replace('\\', '/', $full_path));
                $file_name=end($file_name_parts);
                $filename_ext = pathinfo($file_name, PATHINFO_EXTENSION);

                if(strpos($full_path, '#') !== false || strpos($full_path, '@') !== false || strpos($full_path, '%22') !== false || !in_array($filename_ext, $whitelist_type)){
                    $problem = !in_array($filename_ext, $whitelist_type) ? 'File non presente nella lista delle estensioni ammesse' : 'File da rinominare: contiene #, @ o %22';
                    echo 'IMMAGINE NON IN LISTA ESTENSIONI O DA RINOMINARE: '.$full_path.'<br>';
                    addVerifyLine($verify_log_path, $fields[$i], $id, $file_name, $problem);
                    $to_store[] = $full_path;
                    continue;
                }

                $absolute_path = dirname(__FILE__).$full_path;
                if(!is_file($absolute_path)){
                    echo 'FILE NON TROVATO: '.$full_path.'<br>';
                    addVerifyLine($verify_log_path, $fields[$i], $id, $file_name, 'File non trovato sul server');
                    $to_store[] = $full_path;
                    continue;
                }

                echo '#PROCESSANDO'.$full_path.'<br>';
                $file_size = filesize($absolute_path);
                list($width, $height, $type, $attr) = getimagesize($absolute_path);

                $file_ext = 'image/jpeg';
                if($filename_ext == 'bmp'){$file_ext = 'image/bmp';}
                if($filename_ext == 'gif'){$file_ext = 'image/gif';}
                if($filename_ext == 'png'){$file_ext = 'image/png';}
                if($filename_ext == 'HEIC'){$file_ext = 'image/heic';}

                if (in_array($filename_ext, $whitelist_type)){ $isAllowedImage = true; }

                if($isAllowedImage){
                    if($width<12000 && $height<12000 && $file_size<10000000){ // parametri cloudflare
                        $cloudFlareImages = new StoreCloud();
                        $newfilename_clean = $cloudFlareImages->cleanString($file_name, $filename_ext);
                        $newfilename = $newfilename_clean . '.' . $filename_ext;
                        $imageId = $cloudFlareImages->uploadImageFileFromURL($full_path, $file_name, false, ['customer'=>$app->config['customer_name'],'intent'=>'archy_art_catalog','field'=>$fields[$i], 'extension'=>$file_ext, 'file_name'=>$newfilename], '');
                        $uploaded_file_size = $file_size;
                        $uploaded_file_type = $file_ext;
                    }else{
                        echo 'IMMAGINE OLTRE I 10 MB: '.$full_path.'<br>';

                        if($width<12000 && $height<12000){
                            $d = optimize($absolute_path,$file_name);
                            $file_size_compress = filesize(dirname(__FILE__).'/assets/storage/temp/'.$file_name);
                            $cloudFlareImages = new StoreCloud();
                            $newfilename_clean = $cloudFlareImages->cleanString($file_name, $filename_ext);
                            $newfilename = $newfilename_clean . '.' . $filename_ext;
                            $imageId = $cloudFlareImages->uploadImageFileFromURL('/assets/storage/temp/'.$file_name, $newfilename, false, ['customer'=>$app->config['customer_name'],'intent'=>'archy_art_catalog','field'=>$fields[$i], 'extension'=>$file_ext, 'file_name'=>$newfilename], '');
                            $uploaded_file_size = $file_size_compress;
                            $uploaded_file_type = $file_ext;
                            unlink(dirname(__FILE__).'/assets/storage/temp/'.$file_name);  // CANCELLO IMMAGINE COMPRESSA DOPO ESSERE STATA CARICATA SU CLUODFLARE
                        }else{
                            echo 'IMMAGINE OLTRE I 10 MB E CHE SUPERA LE DIMENSIONI CONSENTITE IN PIXEL: '.$full_path.'<br>';
                            addVerifyLine($verify_log_path, $fields[$i], $id, $file_name, 'Immagine oltre i 10 MB e dimensioni pixel non consentite');
                        }
                    }

                    if(isset($imageId) && $imageId != '' && isset($newfilename) && $newfilename != '' && isset($uploaded_file_size) && isset($uploaded_file_type)){
                        $to_store[] = $imageId;
                        $has_uploaded_files = true;

                        $storeFileRepository->insert([
                            'insert_id' => $id,
                            'field_intent' => 'archy_art_catalog',
                            'field_name' => $fields[$i],
                            'cloud_id' => $imageId,
                            'name' => $newfilename,
                            'location' => 'cloudImages',
                            'size' => $uploaded_file_size,
                            'type' => $uploaded_file_type
                        ]);

                        $statsCloudStorageService->track([
                            'name' => $newfilename,
                            'type' => $uploaded_file_type,
                            'tmp_name' => '',
                            'error' => UPLOAD_ERR_OK,
                            'size' => $uploaded_file_size
                        ], 'cloudImages');
                    }else{
                        $to_store[] = $full_path;
                    }
                }else{
                    echo 'IMMAGINE NON IN LISTA ESTENSIONI O DA RINOMINARE: '.$full_path.'<br>';
                    $to_store[] = $full_path;
                }
            }

            if($has_uploaded_files && count($to_store)>0){
                $to_store_db_value = ($is_multiple_value || count($to_store)>1) ? serialize($to_store) : $to_store[0];
                $to_store_db=$mysqli->real_escape_string($to_store_db_value);
                $mysqli->query("update archy_art_catalog set {$fields[$i]}='".$to_store_db."' where id=".$id);
            }
        }
        for($i=0;$i<count($fields_m);$i++){
            $stored_value = $rows->{$fields_m[$i]};
            if(strpos((string)$stored_value, 'assets') === false){
                continue;
            }
            if(strpos((string)$stored_value, 'assets') !== false){ echo 'TROVATO IN <strong>'.$fields_m[$i].'</strong> nell\'opera numero <strong>'.$id.'</strong> un file :'.$stored_value.'<br>'; }
            $is_multiple_value = isStoredFileCollection($stored_value);
            $stored_paths = getStoredFilePaths($stored_value);
            $to_store = [];
            $has_uploaded_files = false;

            foreach($stored_paths as $full_path){
                if(!is_string($full_path) || trim($full_path) == ''){
                    continue;
                }

                if(strpos($full_path, 'assets') === false){
                    $to_store[] = $full_path;
                    continue;
                }

                if(strpos($full_path, '#') !== false || strpos($full_path, '@') !== false || strpos($full_path, '%22') !== false){
                    echo 'FILE DA RINOMINARE: '.$full_path.'<br>';
                    addVerifyLine($verify_log_path, $fields_m[$i], $id, $full_path, 'File da rinominare: contiene #, @ o %22');
                    $to_store[] = $full_path;
                    continue;
                }

                $absolute_path = dirname(__FILE__).$full_path;
                if(!is_file($absolute_path)){
                    echo 'FILE NON TROVATO: '.$full_path.'<br>';
                    addVerifyLine($verify_log_path, $fields_m[$i], $id, $full_path, 'File non trovato sul server');
                    $to_store[] = $full_path;
                    continue;
                }

                $file_name_parts=explode('/', str_replace('\\', '/', $full_path));
                $file_name=end($file_name_parts);
                $file_size = filesize($absolute_path);
                $file_for_upload = buildUploadFileFromServer($absolute_path, $file_name);
                $upload = $cloudFlareStorage->uploadFile($file_for_upload);

                if(isset($upload['status']) && $upload['status'] == 'success'){
                    $cloud_file_name = $upload['file_name'];
                    $to_store[] = $cloud_file_name;
                    $has_uploaded_files = true;
                    $storeFileRepository->insert([
                        'insert_id' => $id,
                        'field_intent' => 'archy_art_catalog',
                        'field_name' => $fields_m[$i],
                        'cloud_id' => $cloud_file_name,
                        'name' => $upload['file_original_name'],
                        'location' => 'storage',
                        'size' => $file_size,
                        'type' => $upload['mime_type']
                    ]);

                    $statsCloudStorageService->track($file_for_upload, 'storage');

                    echo '#STORAGE '.$full_path.' => '.$cloud_file_name.'<br>';
                    unlink($absolute_path);
                }else{
                    $message = isset($upload['message']) ? $upload['message'] : 'Upload R2 non riuscito';
                    echo 'ERRORE STORAGE: '.$full_path.' - '.$message.'<br>';
                    addVerifyLine($verify_log_path, $fields_m[$i], $id, $file_name, $message);
                    $to_store[] = $full_path;
                }
            }

            if($has_uploaded_files && count($to_store)>0){
                $to_store_db_value = ($is_multiple_value || count($to_store)>1) ? serialize($to_store) : $to_store[0];
                $to_store_db=$mysqli->real_escape_string($to_store_db_value);
                $mysqli->query("update archy_art_catalog set {$fields_m[$i]}='".$to_store_db."' where id=".$id);
            }
        }
        
    }


?>