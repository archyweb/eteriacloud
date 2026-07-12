<?php
class StatsCloudStorageService extends StoreCloud{

    protected $table = 'archy_stats'; // cambia se necessario
    protected $rowId = 1; // id della riga stats

    /**
     * Aggiorna statistiche in base al file
     */
    public function track($file, $storage = 'storage') {

        $size = (int)$file['size'];
        $type = $this->detectType($file);

        $columns = $this->resolveColumns($type, $storage);

        if (!$columns) return false;

        $sql = "
            UPDATE {$this->table}
            SET {$columns['total']}  = {$columns['total']} + 1,
                {$columns['weight']} = {$columns['weight']} + ?
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $size, $this->rowId);
        $stmt->execute();

        return true;
    }
    
    /*
     * Decrementa statistiche dopo delete
     */
    public function decrement($file) {
        
        
        $size = (int)$file['file_media_size'];
        $type = $this->detectTypeFromMime($file['file_media_type']);
        $storage = $file['file_location'];

        $columns = $this->resolveColumns($type, $storage);

        if (!$columns) return false;

        $sql = "
            UPDATE {$this->table}
            SET {$columns['total']}  = GREATEST({$columns['total']} - 1, 0),
                {$columns['weight']} = GREATEST({$columns['weight']} - ?, 0)
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $size, $this->rowId);
        $stmt->execute();

        return true;
    }
    
    /*
     * Riconosce tipo file da $_FILES
     */
    protected function detectType($file) {

        return $this->detectTypeFromMime($file['type'] ?? '');
    }

    /**
     * Riconosce tipo file
     */
    protected function detectTypeFromMime($mime) {

        if (str_starts_with($mime, 'image/')) {
            return 'img';
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        return 'attachments';
    }

    /**
     * Associa tipo + storage alle colonne DB
     */
    protected function resolveColumns($type, $storage) {

        $map = [

            'cloudImages' => [
                'img' => [
                    'total'  => 'stats_total_cloud_img',
                    'weight' => 'stats_weight_cloud_img'
                ],
                'video' => [
                    'total'  => 'stats_total_cloud_video',
                    'weight' => 'stats_weight_cloud_video'
                ]
            ],
            
            'storage' => [
                'img' => [
                    'total'  => 'stats_total_cloud_attachments',
                    'weight' => 'stats_weight_cloud_attachments'
                ],
                'video' => [
                    'total'  => 'stats_total_cloud_attachments',
                    'weight' => 'stats_weight_cloud_attachments'
                ],
                'attachments' => [
                    'total'  => 'stats_total_cloud_attachments',
                    'weight' => 'stats_weight_cloud_attachments'
                ],
            ],
            
            'local' => [
                'img' => [
                    'total'  => 'stats_total_local_img',
                    'weight' => 'stats_weight_local_img'
                ],
            ]
        ];

        return $map[$storage][$type] ?? null;
    }
    
    public function getRemoteFileSize($url){
        $headers = get_headers($url, 1);

        if (isset($headers['Content-Length'])) {
            return (int)$headers['Content-Length'];
        }

        return null;
    }
    
    public function getDashboardStats(int $storageLimit = 10737418240): array{
        // 10GB default (in byte)

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = 1");
        $stmt->execute();

        $stats = $stmt->get_result()->fetch_assoc();

        if (!$stats) {
            return [];
        }
        
        $localSize = (int)$stats['stats_weight_local_img'];
        $imagesSize = (int)$stats['stats_weight_cloud_img'];
        $videosSize = (int)$stats['stats_weight_cloud_video'];
        $attachmentsSize = (int)$stats['stats_weight_cloud_attachments'];

        $totalCloud = $localSize + $imagesSize + $videosSize + $attachmentsSize;

        $usedPercent = $storageLimit > 0
            ? round(($totalCloud / $storageLimit) * 100, 2)
            : 0;

        return [
            'images' => [
                'count' => (int)$stats['stats_total_cloud_img'],
                'size_bytes' => $imagesSize,
                'size' => $this->formatBytes($imagesSize)
            ],

            'videos' => [
                'count' => (int)$stats['stats_total_cloud_video'],
                'size_bytes' => $videosSize,
                'size' => $this->formatBytes($videosSize)
            ],

            'attachments' => [
                'count' => (int)$stats['stats_total_cloud_attachments'],
                'size_bytes' => $attachmentsSize,
                'size' => $this->formatBytes($attachmentsSize)
            ],
            
            'local' => [
                'count' => (int)$stats['stats_total_local_img'],
                'size_bytes' => $localSize,
                'size' => $this->formatBytes($localSize)
            ],

            'total' => [
                'size_bytes' => $totalCloud,
                'size' => $this->formatBytes($totalCloud)
            ],

            'storage_limit' => [
                'bytes' => $storageLimit,
                'size' => $this->formatBytes($storageLimit)
            ],

            'used_percent' => $usedPercent,

            // dati pronti per grafici
            'chart' => [
                [
                    'value' => $imagesSize,
                    'name' => 'Images'
                ],
                [
                    'value' => $videosSize,
                    'name' => 'Videos'
                ],
                [
                    'value' => $attachmentsSize,
                    'name' => 'Attachments'
                ],
                [
                    'value' => $localSize,
                    'name' => 'Local'
                ]
            ]
        ];
    }
    
    public function formatBytes($bytes){
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
    
}