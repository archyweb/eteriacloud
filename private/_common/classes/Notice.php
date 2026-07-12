<?php
class Notice{
	
	public $app, $db, $notices, $unread;
	
	public function __construct(){
		
		global $app;
		//$this->app=&$app;
		$this->db=$app->get_db();
		$this->notices=[];
		$this->unread=0;
	}
	
	public function get(){
		
		$this->unread=$this->db->query("select id from archy_notices where seen=0")->num_rows;
		
		$notices=$this->db->query("select * from archy_notices order by id desc limit 7");
		
		while($notice=mysqli_fetch_object($notices)):
			$this->notices[$notice->id]=$notice;
		endwhile;
		
	}
	
	public function set($notice){
		
		$this->db->query("
			insert into archy_notices 
				(notice, intent, intent_id) 
			values 
				('".addslashes($notice['notice'])."','".addslashes($notice['intent'])."','".addslashes($notice['intent_id'])."')
		");
		
	}
	
	public function html(){
		
		$this->get();
		
		$response=new stdClass();
		$response->unread=$this->unread;
		$response->html='';
		
		foreach($this->notices as $id=>$notice):
                        $notice_seen=$notice->seen=='0'? '1':'';
			$class=$notice_seen==''? ' notification-unread':'';
			$intent_id=$notice->intent=='archy_notices'? $notice->id : $notice->intent_id;
			$notice_text=substr(strip_tags($notice->notice),0,100);
			if(strlen($notice->notice)>100):
				$notice_text.='&hellip;';
			endif;
                        if($notice_seen==1){$notice_text = '<strong>'.$notice_text.'</strong>';}
                        
                        $response->html.='<div class="list-group-item">';
                        $response->html.='<a class="notification notification-flush'.$class.' href="#!" data-intent="'.$notice->intent.'" data-intent_id="'.$intent_id.'" data-notice_id="'.$notice->id.'">
                            <div class="notification-avatar">
                              <div class="avatar avatar-2xl me-3">
                                <img class="rounded-circle" src="/admin_template/assets/img/team/1-thumb.png" alt="" />
                              </div>
                            </div>
                            <div class="notification-body">
                              <p class="mb-1">'.$notice_text.'</p>
                              <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">💬</span>'.App::latin_date_time($notice->created_at).'</span>
                            </div>
                          </a>';
			//$response->html.='<li class="notification notification-flush" data-intent="'.$notice->intent.'" data-intent_id="'.$intent_id.'" data-notice_id="'.$notice->id.'">'.$notice_text.'<i>'.App::latin_date_time($notice->created_at).'</i></li>';
                        $response->html.='</div>';
                endforeach;
		
		return $response;
		
	}
	
	public function mark_notices_as_read(){
		$this->db->query("update archy_notices set seen=1");
	}
	
	public function mark_notice_as_read($id){
		$this->db->query("update archy_notices set seen=1 where id=".$id);
	}
	
}
?>