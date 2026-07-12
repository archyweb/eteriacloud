<?php

class StoreLocalAdapter extends StoreCloud {

    public function upload($src,$file_name,$type,$field_name,$target='docs') {

       //$storeImages = new StoreCloud();
        $path='/assets/documents/';
        if($target=='img'){ $path='/assets/storage/'; }
        $physical_path=$this->subdomain_root.$path;
        if(!is_dir($physical_path)):
                mkdir($physical_path,0777);
        endif;
        $filename_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        //$newfilename = round(microtime(true)) . '.' . $filename_ext;
        $newfilename_clean = $this->cleanString($file_name, $filename_ext);
        $newfilename = $newfilename_clean . '.' . $filename_ext;
        //$path='/assets/documents/';
        $prefix='';
        $counter=1;
        while(is_file($physical_path.$prefix.$newfilename)):
                $prefix=$counter.'-';
                $counter++;
        endwhile;
        move_uploaded_file($src, $physical_path.$prefix.$newfilename);
        return $path.$prefix.$newfilename;
    }

    public function delete($fileName) {

        $path = __DIR__ . "/storage/" . $fileName;

        if (file_exists($path)) {
            unlink($path);
        }
    }
}