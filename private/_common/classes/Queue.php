<?php
class Queue{

    public $app, $db;
    public $maxretry;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
        $this->app=$app;
        
        $this->maxretry=3;
        
        $ret=$this->db->query("
            CREATE TABLE IF NOT EXISTS `archy_queue` (
                `id` BIGINT(11) NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(255) DEFAULT NULL,
                `status` VARCHAR(255) DEFAULT NULL,
                `msgid` VARCHAR(255) DEFAULT NULL,
                `datesent` timestamp NULL DEFAULT NULL,
                `subject` VARCHAR(255) DEFAULT NULL,
                `serializedmsg` TEXT DEFAULT NULL,
                `retry` TINYINT(4) NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        show_query_error($ret,$this->db);
	}
    
    public function enqueue($p_email) {
        $msgtosend=$this->db->query("
            SELECT * FROM `archy_queue`
             WHERE `serializedmsg`='".addslashes(serialize($p_email))."'
               AND `datesent` is NULL
        ");
        //print_r($p_email);
        //echo ">>>".serialize($p_email)."<<<";
        $msg=mysqli_fetch_object($msgtosend);
        if(!$msg) {
            $ret=$this->db->query("
                INSERT INTO `archy_queue` SET
                    `email`='".addslashes($p_email['recipient_mail'])."',
                    `subject`='".addslashes($p_email['subject'])."',
                    `serializedmsg`='".addslashes(serialize($p_email))."'
            ");
            show_query_error($ret,$this->db);
        }
    }

    public function dequeue($fnSend) {
        $msgtosend=$this->db->query("
            SELECT * FROM `archy_queue`
             WHERE `datesent` is NULL
               AND `retry` < ".$this->maxretry."
             ORDER BY retry, created_at
             LIMIT 0,1
        ");
        $msg=mysqli_fetch_object($msgtosend);
        if($msg) {
            // print_r(unserialize($msg->serializedmsg));
            $esito=call_user_func($fnSend,unserialize($msg->serializedmsg)); // ...send()
            // $esito=$fnSend($msg->serializedmsg); // ...send()
            if($esito){
                echo 'Message has been sent';
                $this->update($msg,true,'OK');
                return 1;
            }else{
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $esito->ErrorInfo;
                $this->update($msg,false,addslashes($esito->ErrorInfo));
                return -1;
            }
        }else{
            return 0;
        }
    }

    private function update($msg,$sendok,$status) {
        $q="
            UPDATE `archy_queue` SET
                `status`='".addslashes($status)."',
                `retry`=$msg->retry+1
        ";
        if($sendok) {
            $q .= " , `datesent`=now() ";
        }
        $q .= "WHERE `id`=".$msg->id;
        
        $ret=$this->db->query($q);
        show_query_error($ret,$this->db);
    }   

    private function delete($msg) {
        $q="
            DELETE FROM `archy_queue`
             WHERE `id`=".$msg->id."
        ";
        
        $ret=$this->db->query($q);
        show_query_error($ret,$this->db);
    }   
}
