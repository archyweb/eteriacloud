<?php

class CoreUpdate{
	
	public $app, $db;
		
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;		
	}
	
	public function coreCheckVersion($version=false) {
            $filename = ABSPATH . "version.txt";
            $handle = fopen($filename, "r");
            $v = fread($handle, filesize($filename));
            $mv_current = intval(substr($v, 0, 1));
            $v_current = intval(substr($v, -1));
            $v_next = $v_current + 1;
            if($version){ $handle_w = fopen($filename, "w"); fwrite($handle_w, $mv_current . '.' .$v_next); }
            fclose($handle);
            return $v;
        }

        public function coreCheckUpdates($update=false) {
            $v = $this->coreCheckVersion();
            $v_current = intval(substr($v, -1));
            $v_next = $v_current + 1;
            $url = UPDATE_ABSPATH . "efa-update-3.".$v_next.".zip";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_exec($ch);
            if($update){ return $url; }
            else{ return curl_getinfo($ch, CURLINFO_HTTP_CODE); }
            curl_close($ch);
        }

        public function coreGetChangelog() {
            $v = $this->coreCheckVersion();
            $v_current = intval(substr($v, -1));
            $v_next = $v_current + 1;
            $url = UPDATE_ABSPATH . "efa-changelog-3.".$v_next.".txt";
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        public function coreUpdateDB() {
            //$v = $this->coreCheckVersion();
           // $v_current = intval(substr($v, -1));
            //$v_next = $v_current + 1;
            $backup = $this->coreBackupDb();
            $dump = UPDATE_ABSPATH . "efa-update-3.".$v_current.".sql";

            $templine = '';
            $lines = file($dump);
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '')
                    continue;

                $templine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    $this->db->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $this->db->connect_errno . '<br /><br />');
                    $templine = '';
                }
            }
            echo "<p>Aggiornamento avvenuto con successo! Reindirizzamento alla Dashboard in corso...</p>";

        }

        public function coreDoSqlBackup(){
            $this->initDB->coreBackupDb();
        }
        
        public function coreBackupDb($tables = '*'){
            $return = '';
            $dir = ABSPATH."tmp_sql";

            if($tables == '*'){
                    $tables = array();
                    $result = $this->dbh->query('SHOW TABLES');
                    while($row = $result->fetch_row()){
                        $tables[] = $row[0];
                    }
            }else{
                    $tables = is_array($tables) ? $tables : explode(',',$tables);
            }

            foreach($tables as $table){
                    $result = $this->dbh->query('SELECT * FROM '.$table);
                    $num_fields = $result->field_count;

                    $return.= 'DROP TABLE '.$table.';';
                    $result_2 = $this->dbh->query('SHOW CREATE TABLE '.$table);
                    $row2 = $result_2->fetch_row();
                    $return.= "\n\n".$row2[1].";\n\n";

                    for ($i = 0; $i < $num_fields; $i++) {
                            while($row = $result->fetch_row()){
                                    $return.= 'INSERT INTO '.$table.' VALUES(';
                                    for($j=0; $j<$num_fields; $j++) {
                                            $row[$j] = addslashes($row[$j]);
                                            $row[$j] = str_replace("\n","\\n",$row[$j]);
                                            if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                                            if ($j<($num_fields-1)) { $return.= ','; }
                                    }
                                    $return.= ");\n";
                            }
                    }
                    $return.="\n\n\n";
            }
            if(!is_dir($dir)){ mkdir($dir, 0755 ); }

            $handle = fopen($dir.'/db-backup-'.date("d-m-Y").'-'.(md5(implode(',',$tables))).'.sql','w+');
            fwrite($handle,$return);
            fclose($handle);
        }

        public function coreDoUpdate() {
            $this->efa_doSqlBackup();
            echo "<p>Salvataggio database attuale in corso...</p>";

            $url = $this->efa_checkUpdates(true);

            $zipFile = ABSPATH . 'tmp/temp_update.zip';
            $zipResource = fopen($zipFile, "w");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FILE, $zipResource);
            $page = curl_exec($ch);
            if (!$page) {
                echo "<p>Errore :- " . curl_error($ch) . "</p>";
            }else{
                echo "<p>Scarico il pacchetto aggiornamenti...</p>";
                sleep(3);
                $this->efa_extractZip($zipFile);
            }
            curl_close($ch);
        }

        public function coreExtractZip($zipFile) {
            $zip_folder = array();
            $zip = new ZipArchive;
            $zip = zip_open($zipFile);

            if ($zip) {
                echo "<p>Controllo le cartelle modificate...</p>";
                while ($zip_entry = zip_read($zip)) {
                    if(zip_entry_filesize($zip_entry)==0){
                        array_push($zip_folder, zip_entry_name($zip_entry));
                    }
                }
                zip_close($zip);
                foreach ($zip_folder as $value) {
                    delete_dir(ABSPATH . $value);
                }
                $this->efa_copyZipToCore($zipFile);
            }else{
                echo "<p>Errore :- Impossibile aprire il file zip</p>";
            }
        }

        public function coreCopyZipToCore($zipFile) {
            $zip = new ZipArchive;
            if($zip->open($zipFile) != "true"){
                echo "<p>Errore :- Impossibile aprire il file zip</p>";
            }else{
                echo "<p>Inizio la copia dei nuovi file...</p>";
                $zip->extractTo(ABSPATH);
            }
            $zip->close();
            $this->efa_deleteZip($zipFile);
            $this->efa_updateDB();
        }

        public function coreDeleteZip($zipFile) {
            echo "<p>Cancello lo zip...</p>";
            unlink($zipFile);
            $this->efa_checkVersion(true);
            unset($_SESSION['update']);
        }

	
}

?>