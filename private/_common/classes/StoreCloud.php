<?php

class StoreCloud
{
    /**
     * Cloudflare Account ID.
     * @var string
     */
    protected $cf_account_id = '';

    /**
     * Cloudflare API Tokens.
     *
     * @var string
     */
    protected $cf_api_token = '';
    protected $notify_api_token = '';
    /**
     * CloudFlare Images Hash.
     *
     * @var string
     */
    protected $cf_images_hash = '';
    protected $cf_customer_hash = '';
    /**
     * Domain for Cloudflare Images.
     * @var string
     */
    
    
    /* Cloudflare Storage R2 api */
    protected $cf_storage_access_key = '';
    protected $cf_storage_secret_key = '';
    protected $cf_storage_bucket_name;
    protected $cf_storage_bucket_link;
    
    protected $domain,$subdomain_root;
    protected $db, $app, $config;
    private $env = [];
    /**
     * 
     * Set the Cloudflare Account ID, API Token and Images Hash while initializing the class.
     *
     * @param string $cf_account_id
     * @param string $cf_api_token
     * @param string $cf_images_hash
     * @param string $domain
     */
   
    public function __construct()
    {
        global $app;
        $this->app=$app;
        $this->db=$app->get_db();
        $this->subdomain_root=$this->app->subdomain_root;
        $this->config=require $this->subdomain_root.'/config-local.php';
        $this->env = $this->loadEnv();
        
        $this->cf_account_id = $this->envValue('STORECLOUD_CF_ACCOUNT_ID');
        $this->cf_api_token = $this->envValue('STORECLOUD_CF_API_TOKEN');
        $this->notify_api_token = $this->envValue('STORECLOUD_NOTIFY_API_TOKEN');
        $this->cf_images_hash = $this->envValue('STORECLOUD_CF_IMAGES_HASH');
        $this->cf_customer_hash = $this->envValue('STORECLOUD_CF_CUSTOMER_HASH');
        $this->cf_storage_access_key = $this->envValue('STORECLOUD_R2_ACCESS_KEY');
        $this->cf_storage_secret_key = $this->envValue('STORECLOUD_R2_SECRET_KEY');
        $this->cf_storage_bucket_name = $this->config['customer_bucket'];
        $this->cf_storage_bucket_link = $this->config['customer_bucket_link'];
    }

    private function loadEnv(): array
    {
        $envPath = dirname(__DIR__) . '/.env';
        if (!is_readable($envPath)) {
            return [];
        }

        $env = [];
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        return $env;
    }

    private function envValue(string $key, string $default = ''): string
    {
        if (isset($this->env[$key]) && $this->env[$key] !== '') {
            return $this->env[$key];
        }

        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }

        return $default;
    }
    
    

    /**
     * Makes a POST request to the given URL and returns the response.
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string
     * @throws Exception
     */
    private function post(string $url, array $data = [], array $headers = []): string
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            } else {
                return $response;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Makes a DELETE request to the given URL and returns the response.
     *
     * @param string $url
     * @param array $headers
     * @return string
     * @throws Exception
     */
    private function delete(string $url, array $headers = []): string
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            } else {
                return $response;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Makes a DELETE request to the given URL and returns the response.
     *
     * @param string $url
     * @param array $headers
     * @return string
     * @throws Exception
     */
    private function get(string $url, array $headers = []): string
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            } else {
                return $response;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    public function uploadToCloud($file,$intent){ // $src,$file_name,$intent,$field_name,$file_ext
            global $app;
            //$storeImages = new StoreCloud();
            //$StatsCloudStorageService = new StatsCloudStorageService($this->app);
            $physical_path=$this->subdomain_root.'/assets/images/';
            $physical_storage_path=$this->subdomain_root.'/assets/storage/';
            if(!is_dir($physical_storage_path)):
                    mkdir($physical_storage_path,0777);
            endif;

           /* $total_cloud_img=0;
            $total_cloud_weight=0;
            $total_local_img=0;
            $total_local_weight=0;*/

            $filename_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_size = filesize($file['tmp_name']);
            list($width, $height) = getimagesize($file['tmp_name']);            

            if($width<12000 && $height<12000 && $file_size<10000000){ // rientra nei parametri cloudflare
                    $newFileName = $this->normalizeFileName($file['name']);
                    //$newfilename = $newfilename_clean . '.' . $filename_ext;
                    $imageId = $this->uploadImageFile($file['tmp_name'], $newFileName, false, ['customer'=>$app->config['customer_name'],'intent'=>$intent,'field'=>$file['field'], 'extension'=>$file['type'], 'file_name'=>$newfilename], '');
                    //$this->db->query("update archy_scheda_catalogo set $field_name='".$imageId."' where id=".$this->insert_id); // setto id immagine creata da cloudflare
                    /*$total_cloud_img++;
                    $total_cloud_weight+=$storeImages->formatBytes($file_size,1);*/

            }else{ // per le immagini pesanti
                    $storeLocalAdapter = new StoreLocalAdapter();
                    $new_fullpath_filename=$storeLocalAdapter->upload($file['tmp_name'],$file['name'],'',$file['field_name'],'img'); //carico immagine su server
                    $new_fullpath_filename_explode=explode('/',$new_fullpath_filename);
                    $new_filename = end($new_fullpath_filename_explode);
                    if($width<12000 && $height<12000){
                           /* $total_local_img++;
                            $total_local_weight+=$storeImages->formatBytes(filesize($this->subdomain_root.$new_fullpath_filename),1);*/

                            $this->optimize($this->subdomain_root.$new_fullpath_filename,$new_filename); // comprimo l'immagine salvandola in un altra cartella
                            $file_size_compress = filesize($physical_storage_path.'temp/'.$new_filename);
                            $imageId = $this->uploadImageFileFromURL('/assets/storage/temp/'.$new_filename, $new_filename, false, ['customer'=>$app->config['customer_name'],'intent'=>$intent,'field'=>$file['field'], 'extension'=>$file['type'], 'file_name'=>$new_filename], '');
                            //$this->db->query("update archy_scheda_catalogo set $field_name='".$imageId."' where id=".$this->insert_id); // setto id immagine creata da cloudflare

                           /* $total_cloud_img++;
                            $total_cloud_weight+=$storeImages->formatBytes($file_size_compress,1);  */    
                            unlink($physical_storage_path.'temp/'.$new_filename); // cancello immagine compressa
                    }
            }
           // $StatsCloudStorageService->track($file, 'cloud');
            /*$this->db->query("update archy_stats set stats_total_cloud_img=stats_total_cloud_img+".$total_cloud_img.",stats_weight_cloud_img=stats_weight_cloud_img+".$total_cloud_weight.",stats_total_local_img=stats_total_local_img+".$total_local_img.",stats_weight_local_img=stats_weight_local_img+".$total_local_weight);
            $StatsCloudStorageService->track([
                    'total_cloud_img' => $total_cloud_img,
                    'total_cloud_weight'      => $total_cloud_weight,
                    'total_local_img'      => $total_local_img,
                    'total_local_weight'      => $total_local_weight
                ]);*/
            return [
                "status"    => "success",
                "file_name" => $imageId,
                "file_original_name" => $newFileName
            ];
    }
 
    /**
     * Uploads an image from form to Cloudflare Images and returns the image id.
     *
     * @param string $image
     * @param string $name
     * @param bool $delete_after_upload (optional)
     * @param array $meta_data (optional)
     * @param string $custom_id (optional - max 1024 characters)
     * @return string
     * @throws Exception
     */
    public function uploadImageFile(string $image, string $name, bool $delete_after_upload = false, array $meta_data = [], string $custom_id = ''): string
    {
        try {
            $payload = [
                'file' => new \CURLFile($image, mime_content_type($image), $name),
            ];
            if ($meta_data) {              
                $payload['metadata'] = json_encode($meta_data);
            }
            if ($custom_id) { 
                if (strlen($custom_id) > 1024) {
                    throw new Exception('$custom_id must be <= 1024 characters.');
                }
                $payload['id'] = $custom_id;
            }
            $response = self::post("https://api.cloudflare.com/client/v4/accounts/".$this->cf_account_id."/images/v1", $payload, ["Authorization: Bearer ".$this->cf_api_token]);

            $response = json_decode($response);
            if ($response->success) {
                if ($delete_after_upload) {
                    unlink($image);
                }

                return $response->result->id;
            } else {
                throw new Exception($response->errors[0]->message);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    public function uploadImageFileFromURL(string $image, string $name, bool $delete_after_upload = false, array $meta_data = [], string $custom_id = ''): string
    {
        try {
            $payload = [
                'url' => 'https://'.$_SERVER['HTTP_HOST'].$image,
            ];
            if ($meta_data) {              
                $payload['metadata'] = json_encode($meta_data);
            }
            if ($custom_id) { 
                if (strlen($custom_id) > 1024) {
                    throw new Exception('$custom_id must be <= 1024 characters.');
                }
                $payload['id'] = $custom_id;
            }
            $response = self::post("https://api.cloudflare.com/client/v4/accounts/".$this->cf_account_id."/images/v1", $payload, ["Authorization: Bearer ".$this->cf_api_token]);

            $response = json_decode($response);
            if ($response->success) {
                if ($delete_after_upload) {
                    unlink($image);
                }

                return $response->result->id;
            } else {
                //throw new Exception($response->errors[0]->message);
                error_log('ERRORE IMMAGINE: '.$image);
            }
        } catch (Exception $e) {
            //throw new Exception($e->getMessage());
            error_log('ERRORE IMMAGINE: '.$image);
        }
    }
    
    
    
    /**
     * Deletes an image from Cloudflare Images.
     *
     * @param string $image_id
     * @return bool
     */
    public function deleteImage(string $image_id): bool
    {
        try {
            $response = self::delete("https://api.cloudflare.com/client/v4/accounts/".$this->cf_account_id."/images/v1/{$image_id}", [
                "Authorization: Bearer ".$this->cf_api_token
            ]);
            $response = json_decode($response);
            if ($response->success) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Gets the image id for the given image url.
     *
     * @param string $image_url
     * @param string $variant
     * @return string
     */
    public function getImageIdFromUrl(string $image_url, string $variant = 'public'): string
    {
        //return "{$this->domain}/cdn-cgi/imagedelivery/{$this->cf_images_hash}/{$image_id}/{$variant}";
        $image_url_end = str_replace('https://imagedelivery.net/'.$this->cf_images_hash.'/','',$image_url);
		$image_id = str_replace('/'.$variant,'',$image_url_end);
		return $image_id;
    }
    
    /**
     * Gets the image url for the given image id.
     *
     * @param string $image_id
     * @param string $variant
     * @return string
     */
    public function getImageUrl(string $image_id, string $variant = 'public'): string
    {
        //return "{$this->domain}/cdn-cgi/imagedelivery/{$this->cf_images_hash}/{$image_id}/{$variant}";
        return "https://imagedelivery.net/".$this->cf_images_hash."/{$image_id}/{$variant}";
    }
    
    
    public function getOriginalImage(string $image_id): string
    {
        try {
            $response = self::get("https://api.cloudflare.com/client/v4/accounts/".$this->cf_account_id."/images/v1/{$image_id}/blob", [
                "Authorization: Bearer ".$this->cf_api_token
            ]);
            //$response = json_decode($response);
            return $response;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Gets the image details for the given image id.
     *
     * @param string $image_id
     * @param string $variant
     * @return string
     */
    public function getImageDetail(string $image_id, string $detail_type, string $meta_detail = ''): string
    {
        try {
            $response = self::get("https://api.cloudflare.com/client/v4/accounts/".$this->cf_account_id."/images/v1/{$image_id}", [
                "Authorization: Bearer ".$this->cf_api_token
            ]);
            $response = json_decode($response);
            if ($response->success) {
                if($detail_type == 'meta'){ 
                    if(isset($response->result->meta->$meta_detail) && $response->result->meta->$meta_detail!=null){
                        return $response->result->meta->$meta_detail;
                    }else{
                        return $image_id;
                    }                    
                }else{ return $response->result->$detail_type; }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getLocationFromDb(string $file_name): string
    {
            //error_log("select ".$detail_type." from archy_files where file_cloud_id='".$image_id);
        try {
            $response=$this->db->query("select file_location from archy_files where file_name='".$file_name."'");
            if($response->num_rows>0):
                return mysqli_fetch_object($response)->file_location;
            else:
                return '';
            endif;
        } catch (Exception $e) {
            return false;
        }
    }
        
    public function getImageDetailFromDb(string $image_id, string $detail_type): string
    {
            //error_log("select ".$detail_type." from archy_files where file_cloud_id='".$image_id);
        try {
            $response=$this->db->query("select ".$detail_type." from archy_files where file_cloud_id='".$image_id."'");
            if($response->num_rows>0):
                return mysqli_fetch_object($response)->$detail_type;
            else:
                return '';
            endif;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function deleteFileFromDb(){
        try {
            $response=$this->db->query("delete from archy_files where file_cloud_id='".$image_id."'");
            if($response->num_rows>0):
                return mysqli_fetch_object($response)->$detail_type;
            else:
                return '';
            endif;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function isJson($string)
{
    if (!is_string($string)) {
        return false;
    }

    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}
    
    public function renderCloudImg($v,$n,$s) {
        $class="";
        $style="";
        if($n!=""){$class="class=\"$n\"";}
        if($s!=""){$style="style=\"$s\"";}
        $r2 = new StoreR2Adapter();
        $repo = new StoreFileRepository();
        
        $response="";
        if($v!=null){
            if($this->app->is_serialized($v)){
                $files = unserialize($v);
                if(is_array($files)) {
                    $response .= '<ul>';
                    foreach ($files as $file) {
                        if($file!="") {
                            $file_info=$repo->findAll($file);
                            if(strpos($file, 'assets') === false){
                                if($file_info['file_location']=='cloudImages'){
                                    $response .= '<li><a href="'.$this->getImageUrl($file).'" target="_blank">'.$repo->getFileName($file).'</a></li>';
                                }else{
                                    $response .= '<li><a href="'.$r2->getFileUrl($file).'" target="_blank">'.$repo->getFileName($file).'</a></li>';
                                }
                            }else{
                                $response .= "<img src=\"".$this->subdomain_root.$file."\" $class $style>";
                            }
                        }
                    }
                    $response .= '</ul>';
                }
            }elseif($this->isJson($v)){
                $files = json_decode($v);
                if(is_array($files)) {
                    $response .= '<ul>';
                    foreach ($files as $file) {
                        if($file!="") {
                            $file_info=$repo->findAll($file);
                            if($file_info['file_location']=='cloudImages'){
                                $response .= '<li><a href="'.$this->getImageUrl($file).'" target="_blank">'.$repo->getFileName($file).'</a></li>';
                            }else{
                                $response .= '<li><a href="'.$r2->getFileUrl($file).'" target="_blank">'.$repo->getFileName($file).'</a></li>';
                            }
                        }
                    }
                    $response .= '</ul>';
                }
            }else{
                if($v!="") {
                    if(strpos($v, 'assets') === false){
                        $response = "<img src=\"".$this->getImageUrl($v,'public')."\" $class $style>";
                    }else{
                        $response .= "<img src=\"".$this->subdomain_root.$v."\" $class $style>";
                    }
                }
            }
        }
        return $response;
    }
    
    public function replace_accents($string) {
            $string = str_replace(array('à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'), array('a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'), $string);
            return $string;
    }

    public function cleanString($string,$ext) {
            $string = trim($string);
            $string = $this->replace_accents($string);
            $string = preg_replace('/[^a-z0-9\s]/i', '', $string);
            $string = preg_replace('/[^(\x20-\x7F)]*/', '', $string);
            $string = strtolower($string);
            $string = str_replace(" ", "-", $string);
            $string = str_replace("--", "-", $string);
            $string = str_replace($ext, "", $string);
            return $string;
    }
    
    /*public function formatBytes($size, $precision = 0){
        $unit = ['Byte','KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];

        for($i = 0; $size >= 1024 && $i < count($unit)-1; $i++){
            $size /= 1024;
        }
        return round($size, $precision);
        //return round($size, $precision).' '.$unit[$i];
    }*/
    
    public function GetFormatBytes($kilobytes, $format){
        
        if($format == 'MB'){
            return number_format($kilobytes / 1024, 2) . ' MB';
        }
        
    }
    
    public function normalizeFileName($filename) {

        // separa nome ed estensione
        $info = pathinfo($filename);
        $name = $info['filename'];
        $ext  = isset($info['extension']) ? strtolower($info['extension']) : '';

        // converte caratteri accentati in ascii
        $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);

        // minuscolo
        $name = strtolower($name);

        // sostituisce spazi e separatori con "-"
        $name = preg_replace('/[^\w]+/', '-', $name);

        // rimuove "-" iniziali o finali
        $name = trim($name, '-');

        // evita nome vuoto
        if ($name === '') {
            $name = 'file';
        }

        return $ext ? $name . '.' . $ext : $name;
    }
    
    public function getMymeTypes($type){
        switch ($type) {
            case 'video':
                $mimeTypes = [
                    "video/3gpp",             // .3gp (anche audio/video combo)
                    "video/3gpp2",            // .3g2
                    "video/h261",
                    "video/h263",
                    "video/h264",
                    "video/jpeg",             // .jpgv
                    "video/jpm",              // .jpm
                    "video/mj2",              // .mj2
                    "video/mp2t",             // .ts
                    "video/mp4",              // .mp4, .m4v
                    "video/mpeg",             // .mpeg, .mpg, …
                    "video/ogg",              // .ogv
                    "video/quicktime",        // .mov, .qt
                    "video/vnd.fvt",
                    "video/vnd.mpegurl",      // playlist-ish
                    "video/vnd.vivo",
                    "video/webm",             // .webm
                    "video/x-f4v",
                    "video/x-fli",
                    "video/x-flv",            // .flv
                    "video/x-m4v",            // .m4v
                    "video/x-matroska",       // .mkv
                    "video/x-ms-asf",         // .asf, .asx
                    "video/x-msvideo",        // .avi
                    "video/x-sgi-movie"      // .movie                    
                ];
                break;

            default:
                $mimeTypes = [
                    "image/png",          // .png
                    "image/gif",          // .gif
                    "image/jpeg",         // .jpg, .jpeg
                    "image/webp",         // .webp
                    "image/svg+xml",      // .svg
                    "image/heic",         // .heic
                    "image/heif",         // .heif (spesso usato al posto di heic)
                    "image"
                ];
                break;
        }
            
        return $mimeTypes;
    }
    
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
    public function optimize($filePath,$file_name) 
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
        $temp_path=$this->app->subdomain_root.'/assets/storage/temp/';
        if(!is_dir($temp_path)):
                mkdir($temp_path,0777);
        endif;
        $imagick->writeImage($temp_path.$file_name); 

        // Destroy image from memory
        $imagick->destroy();

        return $rawData;
    }
    
	/**
     * Convert html to PDF
     *
     * @param string $html
     * @return bool
     */
    /*public function convertToPdf(string $html): string
    {
        try {
            $metaData = [
            "html" => $html
        ];
            $payload = json_encode($metaData);
            $addStyleTag = array(
                       "content" => "body { font-family: Arial; }"
                    );
            $payload["addStyleTag"] = json_encode($addStyleTag);

            $addParams = array("timeout" => 0);
            $payload["pdfOptions"] = json_encode($addParams);

            $response = self::postRendering("https://api.cloudflare.com/client/v4/accounts/".$this->cf_account_id."/browser-rendering/pdf", $payload, 
                    ["Authorization: Bearer ".$this->cf_api_token, 'Content-Type: application/json']
            );
            
            return $response;
        } catch (Exception $e) {
            return false;
        }
    }*/
	
}
