<?php

class StoreCloudVideo extends StoreCloud
{
  

    public function __construct() {
        parent::__construct();
        
        
    }
    
    /**
     * Makes a PUT request to the given URL and returns the response.
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string
     * @throws Exception
     */

    public function apiPutRequest(string $url, string $apiToken, array $data = null)
    {
        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json'
        ];

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        // Se ci sono dati da inviare nel body
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Errore cURL: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'response' => $response
        ];
    }
    
    /* upload video */
    public static function uploadVideo(string $video, string $name, bool $delete_after_upload = false, array $meta_data = [], string $custom_id = ''): string
    {
        try {
            $payload = [
                'file' => new \CURLFile($video, mime_content_type($video), $name)
            ];

            $response = self::post("https://api.cloudflare.com/client/v4/accounts/".static::$cf_account_id."/stream", $payload, ["Authorization: Bearer ".static::$cf_api_token]);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /* upload video */
    public static function setVideoNotificationUrl()
    {
        
        $url = "https://api.cloudflare.com/client/v4/accounts/".static::$cf_account_id."/stream/webhook";
        $token = static::$cf_api_token;

        $result = self::apiPutRequest($url, $token, [
            'notificationUrl' => 'https://archymedia.archymedia.com/file_cloud_video_notify.php'
        ]);

        print_r($result);
 
    }
    
    public static function getVideoThumbnail(string $video_id, string $variant = 'public'): string
    {
        return "https://customer-".$cf_customer_hash.".cloudflarestream.com/{$image_id}/thumbnails/{$variant}.jpg";
    }
    
    public static function getVideoPreview(string $video_id): string
    {
        return "https://customer-".$cf_customer_hash.".cloudflarestream.com/{$image_id}/watch";
    }
    

}