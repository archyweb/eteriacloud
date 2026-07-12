<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class StoreManager extends StoreCloud {

    protected $r2;
    protected $local;
    protected $repo;
    protected $stats;

    public function __construct($app) {
        parent::__construct($app);

        $this->r2    = new StoreR2Adapter($app);
        $this->local = new StoreLocalAdapter($app);
        $this->repo  = new StoreFileRepository($app);
        $this->stats = new StatsCloudStorageService($app);
    }

    public function store($files, $params = []) {

        $results = [];
        
        $intent = $params['intent'] ?? '';
        $action = $params['action'] ?? '';
        $insert_id = $params['insert_id'] ?? '';
        
        $normalized = $this->normalizeFiles($files);
        $grouped = [];
        
        foreach ($normalized as $file) {
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $field = $file['group'];
            
            $target = $params['targets'][$file['group']] ?? '';
            $target = $target ?: 'storage';
            if ($target === 'storage') {
                $upload = $this->r2->uploadFile($file);                
            }elseif ($target === 'cloudImages') {
                $upload = $this->uploadToCloud($file,$intent);
            } else {                
                $upload = $this->local->upload($file);
            }
            
            $grouped[$field][] = [
                'name' => $upload['file_name'],
                'multiple' => $file['multiple']
            ];
            
            foreach ($grouped as $field => $filesUploaded) {

                $multiple = $filesUploaded[0]['multiple'];

                $names = array_column($filesUploaded, 'name');

                if (!$multiple) {

                    // campo singolo → prendo solo il primo
                    $value = $names[0];
                    
                     // prendo file esistente dal DB
                    $existing = $this->getDbFieldValue($insert_id, $intent, $field);
                    
                    if ($existing) {
                        $this->delete($target,$existing);
                    }

                    
                } else {

                    // campo multiplo → merge con DB
                    $value = $this->checkExistingValue($insert_id, $intent, $field, $names);
                    
                    
                }

                // update DB
                $this->updateField($insert_id, $intent, $field, $value);
            }

            // salva su DB
            $fileId = $this->repo->insert([
                'insert_id' => $insert_id,
                'field_intent' => $intent,
                'field_name' => $field,
                'cloud_id'     => $upload['file_name'],
                'name'   => $upload['file_original_name'],
                'location' => $target,
                'size'     => $file['size'],
                'type'     => $file['type']
            ]);

            // aggiorna statistiche
            $this->stats->track($file, $target);
            
            $results[] = $upload;
        }
        
        return $upload;
    }
    
    public function checkExistingValue($insert_id, $intent, $field, $names = '', $fileCloudId = ''){
        $existing = $this->getDbFieldValue($insert_id, $intent, $field);

        $existingFiles = json_decode($existing, true);

        if (!is_array($existingFiles)) {
            $existingFiles = $existing ? [$existing] : [];
        }
        
        // 2️⃣ rimuovo il file dall'array
        if($fileCloudId!=''){
            $existingFiles = array_values(array_filter($existingFiles, function ($f) use ($fileCloudId) {
                return $f !== $fileCloudId;
            }));
        }
        
        if($names!=''){
            $merged = array_merge($existingFiles, $names);

            $merged = array_values(array_unique($merged));

        }else{
            $merged = $existingFiles;
        }
        $value = json_encode($merged);
        
        return $value;
    }

    public function delete($target,$fileCloudId,$direct=false) {

        $fileId = $this->repo->find($fileCloudId);
        $fileData = $this->repo->findAll($fileCloudId);
        
        if (!$fileId) return false;

        if ($target === 'storage') {
            $upload = $this->r2->deleteFile($fileCloudId);
        }elseif ($target === 'cloudImages') {
            $this->deleteImage($fileCloudId);
        } else {
            $this->local->delete($fileCloudId);
        }
        
        /* IN CASO DI CANCELLAZIONE DIRETTA DEL FILE DEVO CHIAMARE UNA FUNZIONE PER TOGLIERLO DAL JSON E UPDATE CAMPO DB */
        if($direct){            
            
            $value = $this->checkExistingValue($fileData['file_reference_id'], $fileData['file_reference_table'], $fileData['file_reference_field'],'', $fileCloudId);
            
            $this->updateField($fileData['file_reference_id'], $fileData['file_reference_table'],  $fileData['file_reference_field'], $value);
        }
        
        $this->stats->decrement($fileData);

        $this->repo->delete($fileId);

        return true;
    }

    public function update($fileId, $newFile) {
        
        // elimina vecchio
        $this->delete($fileId);
        
        // carica nuovo
        return $this->store(['file' => $newFile]);
    }

    protected function normalizeFiles(array $files): array{
        $normalized = [];

        foreach ($files as $field => $data) {

            foreach ($data['name'] as $group => $names) {

                // Caso campo singolo
                if (!is_array($names)) {

                    $normalized[] = [
                        'field'      => $field,
                        'group'      => $group,
                        'index'      => null,
                        'multiple'   => false,
                        'name'       => $data['name'][$group],
                        'type'       => $data['type'][$group],
                        'tmp_name'   => $data['tmp_name'][$group],
                        'error'      => $data['error'][$group],
                        'size'       => $data['size'][$group],
                    ];

                    continue;
                }

                // Caso campo multiplo
                foreach ($names as $i => $name) {

                    $normalized[] = [
                        'field'      => $field,
                        'group'      => $group,
                        'index'      => $i,
                        'multiple'   => true,
                        'name'       => $name,
                        'type'       => $data['type'][$group][$i],
                        'tmp_name'   => $data['tmp_name'][$group][$i],
                        'error'      => $data['error'][$group][$i],
                        'size'       => $data['size'][$group][$i],
                    ];
                }
            }
        }

        return $normalized;
    }
    
    protected function getDbFieldValue($id,$intent, $field){
        $sql = "SELECT {$field} FROM {$intent} WHERE id=? LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        return $row[$field] ?? null;
    }

    protected function updateField($id, $intent, $field, $value){
        $sql = "UPDATE {$intent} SET {$field}=? WHERE id=?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $value, $id);
        $stmt->execute();
    }
}