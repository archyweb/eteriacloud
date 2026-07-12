<?php

class StoreFileRepository extends StoreCloud {

    public function insert($data) {
//$this->db->query("INSERT INTO archy_files VALUES (NULL, '".$this->insert_id."', '".$intent."','".$field_name."', 0, 0, 1, '".$params['file_value']."', '".$file_name."','".$params['file_type']."','".$filename_ext."','','".$params['file_location']."', NULL, NULL)"); 
        $stmt = $this->db->prepare("
            INSERT INTO archy_files
            (file_reference_id,
            file_reference_table,
            file_reference_field,
            file_cloud_id,
            file_name,
            file_media_type,
            file_media_size,
            file_location)
            VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssss",
            $data['insert_id'],
            $data['field_intent'],
            $data['field_name'],
            $data['cloud_id'],
            $data['name'],
            $data['type'],
            $data['size'],
            $data['location']
        );

        $stmt->execute();

        return $stmt->insert_id;
    }
    
    public function getFilePreview($fileName, $url){
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $images = ['jpg','jpeg','png','gif','webp'];

        if (in_array($ext, $images)) {
            return '<img src="'.$url.'" class="preview-img" style="max-height:100px">';
        }

        $icons = [
            'pdf' => 'far fa-file-pdf',
            'zip' => 'far fa-file-archive',
            'doc' => 'far fa-file-word',
            'docx'=> 'far fa-file-word',
            'xls' => 'far fa-file-excel',
            'xlsx'=> 'far fa-file-excel'
        ];

        $icon = $icons[$ext] ?? 'far fa-file-alt';

        return '<span class="'.$icon.' fs-4"></span>';
    }
    
    public function getFilesFromDb($id,$field,$intent){
        $stmt = $this->db->prepare("SELECT file_cloud_id FROM archy_files WHERE file_reference_id=? AND file_reference_field=? AND file_reference_table=?");
        $stmt->bind_param("iss", $id,  $field, $intent);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return array_column($result, 'file_cloud_id');

    }

    public function find($file_cloud_id) {
        $stmt = $this->db->prepare("SELECT id FROM archy_files WHERE file_cloud_id=?");
        $stmt->bind_param("s", $file_cloud_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row['id'] ?? null;
    }
    
    public function getFileName($file_cloud_id){
        $stmt = $this->db->prepare("SELECT file_name FROM archy_files WHERE file_cloud_id=?");
        $stmt->bind_param("s", $file_cloud_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row['file_name'] ?? null;
    }
    
    public function findAll($file_cloud_id) {
        $stmt = $this->db->prepare("SELECT * FROM archy_files WHERE file_cloud_id=?");
        $stmt->bind_param("s", $file_cloud_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM archy_files WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}