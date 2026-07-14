<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class StoreR2Adapter extends StoreCloud
{
    private $s3;
    private $bucket;

    public function __construct() {
        parent::__construct();
        
        $this->bucket = $this->cf_storage_bucket_name;
        $this->ensureStoreCloudConfig([
            'STORECLOUD_CF_ACCOUNT_ID' => $this->cf_account_id,
            'STORECLOUD_R2_ACCESS_KEY' => $this->cf_storage_access_key,
            'STORECLOUD_R2_SECRET_KEY' => $this->cf_storage_secret_key,
            'customer_bucket' => $this->cf_storage_bucket_name
        ], 'Cloudflare R2');

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'auto',
            'endpoint' => "https://{$this->cf_account_id}.r2.cloudflarestorage.com",
            'credentials' => [
                'key'    => $this->cf_storage_access_key,
                'secret' => $this->cf_storage_secret_key,
            ],
        ]);
    }
    
     /**
     * Rootscratch Cloudstorage Upload File
     *
     * @param array $file The uploaded file data from `$_FILES` (e.g., `$_FILES['test']`).
     * @param string|null $category Optional category for the file. (example : 'image' / 'video' / 'docs' / 'archives')
     * @param string $path The destination path for the uploaded file (default: 'uploads/').
     * @param int|null $sizeLimit Maximum file size in bytes (default: 200MB if null).
     */
    public function uploadFile($file, $category = null, $path = 'uploads/', $sizeLimit = null)
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ["status" => "error", "message" => "Invalid file upload."];
        }
        
        // Set default size limit to 200MB if null
        if ($sizeLimit === null) {
            $sizeLimit = 200 * 1024 * 1024; // 200MB in bytes
        }
        
        // Check file size
        if ($file['size'] > $sizeLimit) {
            $limitInMB = $sizeLimit / (1024 * 1024);
            return ["status" => "error", "message" => "File size exceeds the limit of {$limitInMB}MB."];
        }

        // Define allowed file extensions and MIME types per category
        $allowedCategories = [
            'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'video'    => ['mp4', 'mov', 'avi', 'mkv'],
            'audio'    => ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a', 'webm'],
            'docs'     => ['pdf', 'docx', 'xlsx', 'txt'],
            'archives' => ['zip', 'rar'],
            'database' => ['sql']
        ];

        // MIME types for each file type
        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'mp4'  => 'video/mp4',
            'mov'  => 'video/quicktime',
            'avi'  => 'video/x-msvideo',
            'mkv'  => 'video/x-matroska',
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'ogg'  => 'audio/ogg',
            'aac'  => 'audio/aac',
            'flac' => 'audio/flac',
            'm4a'  => 'audio/mp4',
            'webm' => 'audio/webm',
            'pdf'  => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt'  => 'text/plain',
            'zip'  => 'application/zip',
            'rar'  => 'application/vnd.rar',
            'sql'  => 'application/sql'
        ];

        // Get file extension
        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        // Validate category or specific file type
        if ($category !== null) {
            if (isset($allowedCategories[$category])) {
                // If category is provided, validate against allowed extensions in that category
                if (!in_array($fileExtension, $allowedCategories[$category])) {
                    return [
                        "status"  => "error",
                        "message" => "Invalid format for category '$category'. Allowed: " . implode(", ", $allowedCategories[$category])
                    ];
                }
            } elseif (!isset($mimeTypes[$category])) {
                // If not a category, check if it's a specific file type (e.g., 'pdf', 'txt')
                return ["status" => "error", "message" => "Invalid category or file type specified."];
            } elseif ($fileExtension !== $category) {
                // If a specific type is given, ensure it matches the file extension
                return ["status" => "error", "message" => "Only .$category files are allowed."];
            }
        } else {
            // If no category is provided, allow all supported file types
            $allExtensions = array_merge(...array_values($allowedCategories));
            if (!in_array($fileExtension, $allExtensions)) {
                return ["status" => "error", "message" => "Unsupported file type."];
            }
        }

        // Set correct MIME type
        $contentType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

        // Generate unique filename
        $file_cloud_id = md5(time());
        $fileOriginalName = $this->normalizeFileName($file["name"]);
        $fileName = $file_cloud_id . "_" . $this->normalizeFileName($file["name"]);
        $fileTmp = $file["tmp_name"];
        $fileKey = $path . $fileName; // Store file in the specified folder
        $response = [];
        try {
            // Upload File to Cloudflare R2 with correct Content-Type
            $result = $this->s3->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $fileKey,
                'SourceFile'  => $fileTmp,
                'ContentType' => $contentType, // Set correct MIME type
                'ACL'         => 'public-read',
            ]);
            //return $result;
            // Return the file name and MIME type (no JSON encoding inside the class)
            return [
                "status"    => "success",
                "message"   => "File uploaded successfully.",
                "file_name" => $fileName,
                "file_original_name" => $fileOriginalName,
                "mime_type" => $contentType,
                'etag'      => trim($result['ETag'], '"'),
                'cloud_id' => $file_cloud_id,
                'url'       => $this->publicUrl($fileName),
                'object_url'=> trim($result['ObjectURL'], '"')
            ];
        } catch (AwsException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    
    
    protected function publicUrl($key) {
        return "https://cdn.miosito.com/" . $key;
    }
    
    /**
     * 
     * Sample Code (Convert and Upload)
     * 
     * $convert = $class->base64Image('data:image/png;base64,.');
     * 
     * $upload = $class->uploadFile($convert, null, null);
     * 
     * Rootscratch Base64Image to File
     * @param string example data:image/png;base64,.
     * @param string|null The Default filename is converted.png
     * @return array{
     *      name: string,
     *      type: string,
     *      tmp_name: string,
     *      error: int,
     *      size: int|false
     * }
     */

    public function base64Image($base64_string, $file_name = 'converted.png')
    {
        // Extract the base64 data from the string (remove the "data:image/png;base64," part)
        if (strpos($base64_string, 'base64,') !== false) {
            list(, $base64_string) = explode('base64,', $base64_string);
        }

        // Decode the Base64 string
        $decoded_data = base64_decode($base64_string);

        // Define a temporary file path
        $temp_file = tempnam(sys_get_temp_dir(), 'upload_');

        // Write the decoded image data to the temporary file
        file_put_contents($temp_file, $decoded_data);

        // Get MIME type and extension
        $mime_type = mime_content_type($temp_file);
        $extension = explode('/', $mime_type)[1] ?? 'png';

        // Ensure the correct file extension
        $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '.' . $extension;

        // Simulate the `$_FILES` array structure
        return [
            'name' => $file_name,
            'type' => $mime_type,
            'tmp_name' => $temp_file,
            'error' => 0,
            'size' => filesize($temp_file),
        ];
    }
    
    
    public function deleteFile($fileName, $path = 'uploads/')
    {
        $fileKey = $path . $fileName; // Full path to the file in R2

        try {
            // Delete file from Cloudflare R2
            $result = $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $fileKey,
            ]);
            
            return [
                "status"    => "success",
                "message"   => "File deleted successfully.",
                "file_name" => $fileName
            ];
        } catch (AwsException $e) {
            return [
                "status"  => "error",
                "message" => $e->getMessage()
            ];
        }
    }
    
    public function getFileUrl($fileName){
        return 'https://'.$this->cf_storage_bucket_link.'.archyart.it/uploads/'.$fileName;
    }
    
    public function getFile($fileName, $path = 'uploads/')
    {
        $fileKey = $path . $fileName; // Full path to the file in R2

        try {
            // Delete file from Cloudflare R2
            $result = $this->s3->getObject([
                'Bucket' => $this->bucket,
                'Key'    => $fileKey,
            ]);

            file_put_contents('/home/larry/documents/poetry.pdf', $object['Body']->getContents());
        } catch (AwsException $e) {
            return [
                "status"  => "error",
                "message" => $e->getMessage()
            ];
        }
    }

}
