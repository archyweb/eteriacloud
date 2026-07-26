<?php
class Notice{
	
	public $app, $db, $notices, $unread, $columns;
	
	public function __construct($db=NULL){
		
		global $app;
		//$this->app=&$app;
		$this->db=$db? $db : $app->get_db();
		$this->notices=[];
		$this->unread=0;
		$this->columns=[];
		$this->ensure_columns();
	}
	
	private function ensure_columns(){
		$this->ensure_column('source',"varchar(32) not null default 'local'");
		$this->ensure_column('priority',"varchar(16) not null default 'normal'");
	}
	
	private function ensure_column($column,$definition){
		$column_result=$this->db->query("show columns from archy_notices like '".addslashes($column)."'");
		if($column_result && $column_result->num_rows>0):
			$this->columns[$column]=true;
			return;
		endif;
		$this->db->query("alter table archy_notices add column ".$column." ".$definition);
		$this->columns[$column]=true;
	}
	
	private function has_column($column){
		if(!isset($this->columns[$column])):
			$column_result=$this->db->query("show columns from archy_notices like '".addslashes($column)."'");
			$this->columns[$column]=$column_result && $column_result->num_rows>0;
		endif;
		return $this->columns[$column];
	}
	
	private function categories(){
		return [
			'support'=>['label'=>'Supporto','class'=>'info'],
			'administrative'=>['label'=>'Amministrazione','class'=>'warning'],
			'update'=>['label'=>'Aggiornamenti','class'=>'primary'],
			'general'=>['label'=>'Generale','class'=>'secondary']
		];
	}
	
	public function category_options(){
		return $this->categories();
	}
	
	private function priorities(){
		return [
			'urgent'=>['label'=>'Urgente','class'=>'danger'],
			'normal'=>['label'=>'Normale','class'=>'info'],
			'low'=>['label'=>'Basso','class'=>'secondary']
		];
	}
	
	public function priority_options(){
		return $this->priorities();
	}
	
	private function normalize_priority($priority){
		$priority=strtolower(trim((string)$priority));
		$aliases=[
			'urgenza'=>'urgent',
			'alta'=>'urgent',
			'high'=>'urgent',
			'bassa'=>'low'
		];
		if(isset($aliases[$priority])):
			$priority=$aliases[$priority];
		endif;
		$priorities=$this->priorities();
		return isset($priorities[$priority])? $priority : 'normal';
	}
	
	private function priority_meta($priority){
		$priority=$this->normalize_priority($priority);
		$priorities=$this->priorities();
		return $priorities[$priority];
	}
	
	private function priority_badge_html($priority){
		$priority_meta=$this->priority_meta($priority);
		return '<span class="badge bg-'.$priority_meta['class'].' me-2">'.$priority_meta['label'].'</span>';
	}
	
	private function normalize_source($source){
		$source=strtolower(trim((string)$source));
		$aliases=[
			'admin'=>'archy',
			'archyart'=>'archy',
			'archy_art'=>'archy',
			'sdi'=>'local',
			'billing'=>'local',
			'fatturazione'=>'local',
			'system'=>'local'
		];
		if(isset($aliases[$source])):
			$source=$aliases[$source];
		endif;
		return $source=='archy'? 'archy' : 'local';
	}
	
	private function source_badge_html($source){
		$source=$this->normalize_source($source);
		if($source=='archy'):
			return '<span class="badge bg-primary me-2">Archy</span>';
		endif;
		return '<span class="badge bg-200 text-700 me-2">Interna</span>';
	}
	
	private function source_avatar_html($source){
		$source=$this->normalize_source($source);
		if($source=='archy'):
			return '
				<div class="avatar avatar-2xl me-3">
					<div class="rounded-circle bg-white border d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
						<img src="/admin_template_child/img/logo_archy_art.png" alt="Archy" style="max-width:30px;max-height:30px;" />
					</div>
				</div>
			';
		endif;
		return '
			<div class="avatar avatar-2xl me-3">
				<div class="rounded-circle bg-200 text-700 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
					<span class="fas fa-bell fs-7"></span>
				</div>
			</div>
		';
	}
	
	private function notice_priority($notice){
		return $this->normalize_priority(isset($notice->priority)? $notice->priority : 'normal');
	}
	
	private function notice_source($notice){
		return $this->normalize_source(isset($notice->source)? $notice->source : 'local');
	}
	
	private function order_sql(){
		return "
			case
				when lower(ifnull(priority,'')) in ('urgent','urgenza','alta','high') then 3
				when lower(ifnull(priority,'')) in ('low','bassa') then 1
				else 2
			end desc,
			id desc
		";
	}
	
	private function normalize_category($category){
		$category=strtolower(trim((string)$category));
		$aliases=[
			'admin'=>'administrative',
			'billing'=>'administrative',
			'invoice'=>'administrative',
			'invoices'=>'administrative',
			'fatture'=>'administrative',
			'updates'=>'update',
			'system'=>'update'
		];
		if(isset($aliases[$category])):
			$category=$aliases[$category];
		endif;
		$categories=$this->categories();
		return isset($categories[$category])? $category : 'general';
	}
	
	private function category_sql(){
		return "
			case
				when lower(ifnull(category,'')) in ('support') then 'support'
				when lower(ifnull(category,'')) in ('administrative','admin','billing','invoice','invoices','fatture') then 'administrative'
				when lower(ifnull(category,'')) in ('update','updates','system') then 'update'
				else 'general'
			end
		";
	}
	
	private function category_meta($category){
		$category=$this->normalize_category($category);
		$categories=$this->categories();
		return isset($categories[$category])? $categories[$category] : $categories['general'];
	}
	
	private function category_class($category){
		$class=preg_replace('/[^a-zA-Z0-9_-]/','-', $this->normalize_category($category));
		return $class!=''? $class : 'general';
	}
	
	private function escape_html($value){
		return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
	}
	
	private function notice_parts($notice){
		$html=(string)$notice->notice;
		$title='';
		$body_html=$html;
		
		if(preg_match('/<strong[^>]*>(.*?)<\/strong>\s*(<br\s*\/?>)?/is', $html, $matches)):
			$title=html_entity_decode(trim(strip_tags($matches[1])), ENT_QUOTES, 'UTF-8');
			$body_html=preg_replace('/<strong[^>]*>.*?<\/strong>\s*(<br\s*\/?>)?/is', '', $html, 1);
		endif;
		
		$body_text=html_entity_decode(trim(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $body_html))), ENT_QUOTES, 'UTF-8');
		$full_text=html_entity_decode(trim(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], "\n", $html))), ENT_QUOTES, 'UTF-8');
		
		if($title==''):
			$lines=preg_split('/\r\n|\r|\n/', $full_text);
			foreach($lines as $line):
				$line=trim($line);
				if($line!=''):
					$title=$line;
					break;
				endif;
			endforeach;
		endif;
		
		if($title==''):
			$title='Notifica';
		endif;
		
		if($body_text==''):
			$body_text=$full_text;
		endif;
		
		return [
			'title'=>$title,
			'body'=>$body_text
		];
	}
	
	private function notice_link($notice,$class=''){
		$notice_seen=$notice->seen=='0'? '1':'';
		$notification_class=$notice_seen==''? ' notification-unread':'';
		$intent_id=$notice->intent=='archy_notices'? $notice->id : $notice->intent_id;
		$notice_parts=$this->notice_parts($notice);
		$notice_text=$this->escape_html($notice_parts['title']);
		if($notice_seen==1):
			$notice_text='<strong>'.$notice_text.'</strong>';
		endif;
		return '<a class="notification notification-flush notice-detail-trigger'.$notification_class.' '.$class.'" href="#!" data-intent="'.$notice->intent.'" data-intent_id="'.$intent_id.'" data-notice_id="'.$notice->id.'">'.$notice_text.'</a>';
	}
	
	public function get(){
		
		$this->unread=$this->db->query("select id from archy_notices where seen=0")->num_rows;
		
		$notices=$this->db->query("select * from archy_notices order by ".$this->order_sql()." limit 7");
		
		while($notice=mysqli_fetch_object($notices)):
			$this->notices[$notice->id]=$notice;
		endwhile;
		
	}
	
	public function set($notice){
		
		$fields=[
			'notice'=>isset($notice['notice'])? $notice['notice'] : '',
			'intent'=>isset($notice['intent'])? $notice['intent'] : 'archy_notices',
			'intent_id'=>isset($notice['intent_id'])? $notice['intent_id'] : '0'
		];
		if($this->has_column('category')):
			$fields['category']=$this->normalize_category(isset($notice['category'])? $notice['category'] : 'general');
		endif;
		if($this->has_column('source')):
			$fields['source']=$this->normalize_source(isset($notice['source'])? $notice['source'] : 'local');
		endif;
		if($this->has_column('priority')):
			$fields['priority']=$this->normalize_priority(isset($notice['priority'])? $notice['priority'] : 'normal');
		endif;
		
		$columns=[];
		$values=[];
		foreach($fields as $column=>$value):
			$columns[]=$column;
			$values[]="'".addslashes($value)."'";
		endforeach;
		
		$this->db->query("
			insert into archy_notices 
				(".join(', ', $columns).") 
			values 
				(".join(', ', $values).")
		");
		
	}
	
	private function set_category($category,$notice){
		$notice['category']=$category;
		$this->set($notice);
	}
	
	public function support($notice){
		$this->set_category('support',$notice);
	}
	
	public function administrative($notice){
		$this->set_category('administrative',$notice);
	}
	
	public function update($notice){
		$this->set_category('update',$notice);
	}
	
	public function html(){
		
		$this->get();
		
		$response=new stdClass();
		$response->unread=$this->unread;
		$response->html='';
		
		foreach($this->notices as $id=>$notice):
                        $notice_seen=$notice->seen=='0'? '1':'';
			$class=$notice_seen==''? ' notification-unread':'';
			$category=$this->normalize_category(isset($notice->category)? $notice->category : 'general');
			$category_meta=$this->category_meta($category);
			$category_badge=$category!='general'? '<span class="badge bg-'.$category_meta['class'].' me-2">'.$category_meta['label'].'</span>' : '';
			$priority=$this->notice_priority($notice);
			$source=$this->notice_source($notice);
			$intent_id=$notice->intent=='archy_notices'? $notice->id : $notice->intent_id;
			$notice_parts=$this->notice_parts($notice);
			$notice_text=$this->escape_html($notice_parts['title']);
                        if($notice_seen==1){$notice_text = '<strong>'.$notice_text.'</strong>';}
                        
                        $response->html.='<div class="list-group-item notice-category-'.$this->category_class($category).'">';
                        $response->html.='<a class="notification notification-flush notice-detail-trigger'.$class.'" href="#!" data-intent="'.$notice->intent.'" data-intent_id="'.$intent_id.'" data-notice_id="'.$notice->id.'">
                            <div class="notification-avatar">
                              '.$this->source_avatar_html($source).'
                            </div>
                            <div class="notification-body">
                              <p class="mb-1">'.$this->priority_badge_html($priority).$this->source_badge_html($source).$category_badge.$notice_text.'</p>
                              <span class="notification-time"><span class="me-2" role="img" aria-label="Emoji">💬</span>'.App::latin_date_time($notice->created_at).'</span>
                            </div>
                          </a>';
			//$response->html.='<li class="notification notification-flush" data-intent="'.$notice->intent.'" data-intent_id="'.$intent_id.'" data-notice_id="'.$notice->id.'">'.$notice_text.'<i>'.App::latin_date_time($notice->created_at).'</i></li>';
                        $response->html.='</div>';
                endforeach;
		
		return $response;
		
	}
	
	public function categories_html($limit=3){
		$response=new stdClass();
		$response->unread=$this->db->query("select id from archy_notices where seen=0")->num_rows;
		$response->html='';
		$category_sql=$this->category_sql();
		
		$categories=$this->db->query("
			select 
				".$category_sql." as category,
				count(*) as total,
				sum(case when seen=0 then 1 else 0 end) as unread,
				max(created_at) as last_created_at
			from archy_notices
			group by ".$category_sql."
			order by unread desc, last_created_at desc
		");
		
		if(!$categories || $categories->num_rows==0):
			$response->html='<p class="text-600 mb-0">Nessuna notifica presente.</p>';
			return $response;
		endif;
		
		while($category=mysqli_fetch_object($categories)):
			$category_key=$this->normalize_category($category->category);
			$category_meta=$this->category_meta($category_key);
			$category_class=$this->category_class($category_key);
			$unread=(int)$category->unread;
			$total=(int)$category->total;
			$notices=$this->db->query("
				select *
				from archy_notices
				where ".$category_sql."='".addslashes($category_key)."'
				order by ".$this->order_sql()."
				limit ".(int)$limit
			);
			
			$response->html.='
				<div class="dashboard-notice-category notice-category-'.$category_class.' border-bottom pb-3 mb-3">
					<div class="d-flex flex-between-center mb-2">
						<div>
							<span class="badge bg-'.$category_meta['class'].' me-2">'.$category_meta['label'].'</span>
							<span class="text-600 fs-11">'.$total.' totali</span>
						</div>
						<span class="badge rounded-pill '.($unread>0? 'bg-danger':'bg-200 text-700').'">'.$unread.' nuove</span>
					</div>
			';
			while($notice=mysqli_fetch_object($notices)):
				$response->html.='
					<div class="py-1">'.$this->notice_link($notice,'d-block text-700 text-decoration-none').'</div>
					<div class="text-500 fs-11 mb-1">'.App::latin_date_time($notice->created_at).'</div>
				';
			endwhile;
			$response->html.='</div>';
		endwhile;
		
		return $response;
	}

	public function dashboard_html($limit=6){
		$response=new stdClass();
		$response->unread=$this->db->query("select id from archy_notices where seen=0")->num_rows;
		$response->html='';
		$limit=(int)$limit;
		if($limit<=0):
			$limit=6;
		endif;
		
		$notices=$this->db->query("
			select *
			from archy_notices
			order by ".$this->order_sql()."
			limit ".$limit
		);
		
		if(!$notices || $notices->num_rows==0):
			$response->html='<p class="text-600 mb-0">Nessuna notifica presente.</p>';
			return $response;
		endif;
		
		while($notice=mysqli_fetch_object($notices)):
			$category=$this->normalize_category(isset($notice->category)? $notice->category : 'general');
			$category_meta=$this->category_meta($category);
			$category_class=$this->category_class($category);
			$is_unread=$notice->seen=='0';
			$priority=$this->notice_priority($notice);
			$source=$this->notice_source($notice);
			$notice_parts=$this->notice_parts($notice);
			$notice_text=$this->escape_html($notice_parts['title']);
			$text_class=$is_unread? 'fw-bold text-900':'text-700';
			$button_text=$is_unread? 'Segna letta':'Segna non letta';
			$status_text=$is_unread? 'Non letta':'Letta';
			$status_class=$is_unread? 'bg-danger':'bg-200 text-700';
			
			$response->html.='
				<div class="dashboard-notice-item notice-category-'.$category_class.' '.($priority=='urgent'? 'border-start border-danger border-3 ps-2 bg-danger-subtle':'border-bottom').' pb-2 mb-2">
					<div class="d-flex align-items-start justify-content-between gap-2">
						<div class="min-w-0 dashboard-notice-content cursor-pointer" role="button" data-notice_id="'.$notice->id.'">
							<div class="mb-1">
								'.$this->priority_badge_html($priority).'
								'.$this->source_badge_html($source).'
								<span class="badge bg-'.$category_meta['class'].' me-2">'.$category_meta['label'].'</span>
								<span class="badge rounded-pill '.$status_class.'">'.$status_text.'</span>
							</div>
							<div class="'.$text_class.'">'.$notice_text.'</div>
							<div class="text-500 fs-11 mt-1">'.App::latin_date_time($notice->created_at).'</div>
						</div>
						<button type="button" class="btn btn-falcon-default btn-sm flex-shrink-0 toggle-notice-seen" data-notice_id="'.$notice->id.'">'.$button_text.'</button>
					</div>
				</div>
			';
		endwhile;
		
		return $response;
	}
	
	public function detail_html($id){
		$response=new stdClass();
		$response->success=false;
		$response->html='<p class="text-600 mb-0">Notifica non trovata.</p>';
		$response->title='Dettaglio notifica';
		$response->toggle_label='';
		$id=(int)$id;
		if($id<=0):
			return $response;
		endif;
		
		$result=$this->db->query("select * from archy_notices where id=".$id." limit 1");
		if(!$result || $result->num_rows==0):
			return $response;
		endif;
		
		$notice=mysqli_fetch_object($result);
		$category=$this->normalize_category(isset($notice->category)? $notice->category : 'general');
		$category_meta=$this->category_meta($category);
		$priority=$this->notice_priority($notice);
		$source=$this->notice_source($notice);
		$is_unread=$notice->seen=='0';
		$status_text=$is_unread? 'Non letta':'Letta';
		$status_class=$is_unread? 'bg-danger':'bg-200 text-700';
		$notice_parts=$this->notice_parts($notice);
		$notice_text=nl2br($this->escape_html($notice_parts['body']));
		
		$response->success=true;
		$response->title=$notice_parts['title'];
		$response->toggle_label=$is_unread? 'Segna letta':'Segna non letta';
		$response->html='
			<div class="mb-3">
				'.$this->priority_badge_html($priority).'
				'.$this->source_badge_html($source).'
				<span class="badge bg-'.$category_meta['class'].' me-2">'.$category_meta['label'].'</span>
				<span class="badge rounded-pill '.$status_class.'">'.$status_text.'</span>
			</div>
			<div class="text-500 fs-10 mb-3">'.App::latin_date_time($notice->created_at).'</div>
			<div class="notice-detail-text text-800">'.$notice_text.'</div>
		';
		
		return $response;
	}
	
	public function table_rows_html(){
		$html='';
		$notices=$this->db->query("
			select *
			from archy_notices
			order by ".$this->order_sql()."
		");
		
		if(!$notices):
			return $html;
		endif;
		
		while($notice=mysqli_fetch_object($notices)):
			$category=$this->normalize_category(isset($notice->category)? $notice->category : 'general');
			$category_meta=$this->category_meta($category);
			$priority=$this->notice_priority($notice);
			$priority_meta=$this->priority_meta($priority);
			$source=$this->notice_source($notice);
			$is_unread=$notice->seen=='0';
			$status_text=$is_unread? 'Non letta':'Letta';
			$status_class=$is_unread? 'bg-danger':'bg-200 text-700';
			$notice_parts=$this->notice_parts($notice);
			$title=$this->escape_html($notice_parts['title']);
			$text_class=$is_unread? 'fw-bold text-900':'text-700';
			$priority_order=$priority=='urgent'? 3 : ($priority=='low'? 1 : 2);
			$status_order=$is_unread? 0 : 1;
			
			$html.='
				<tr>
					<td>'.$notice->id.'</td>
					<td class="notice-page-detail '.$text_class.'" role="button" data-notice_id="'.$notice->id.'">'.$title.'</td>
					<td data-order="'.$status_order.'"><span class="badge rounded-pill '.$status_class.'">'.$status_text.'</span></td>
					<td data-order="'.$priority_order.'"><span class="badge bg-'.$priority_meta['class'].'">'.$priority_meta['label'].'</span></td>
					<td><span class="badge bg-'.$category_meta['class'].'">'.$category_meta['label'].'</span></td>
					<td>'.$this->source_badge_html($source).'</td>
					<td data-order="'.$this->escape_html($notice->created_at).'">'.App::latin_date_time($notice->created_at).'</td>
					<td>
						<button type="button" class="btn btn-falcon-default btn-sm notice-page-detail" data-notice_id="'.$notice->id.'">
							<span class="fas fa-search me-1"></span> Dettaglio
						</button>
					</td>
				</tr>
			';
		endwhile;
		
		return $html;
	}
	
	public function mark_notices_as_read(){
		$this->db->query("update archy_notices set seen=1");
	}
	
	public function mark_notice_as_read($id){
		$this->db->query("update archy_notices set seen=1 where id=".(int)$id);
	}
	
	public function toggle_notice_seen($id){
		$id=(int)$id;
		if($id<=0):
			return;
		endif;
		$this->db->query("update archy_notices set seen=if(seen=1,0,1) where id=".$id);
	}
	
}
?>
