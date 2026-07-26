<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Message{
	
	public $app, $db, $root, $d_root, $shortcodes, $bulk_message_chunk, $last_error;
	
	public function __construct(){
		global $app;
		$this->app=&$app;
		$this->db=$app->get_db();
		$this->root=$this->app->main_root;
		$this->d_root=$app->subdomain_root;
		
		$this->shortcodes=[
			'[name]',
			'[surname]',
			'[RagioneSociale/Nominativo]',
			'[DataNascita]',
			'[LuogoNascita]',
			'[PartitaIVA]',
			'[CodiceFiscale]',
			'[Indirizzo]',
			'[zip]',
			'[Comune]',
			'[Provincia]',
			'[SiglaProvincia]',
			'[Regione]',
			'[NazioneIT]',
			'[NazioneEN]',
			'[Telefono]',
			'[Fax]',
			'[Cellulare]',
			'[EMail]',
			'[PEC]',
			'[SitoWeb]',
			'[Tessera]',
			'[DataTessera]',
			'[ScadenzaTessera]',
			'[DataAdesione]',
			'[CategoriaAssociativa]',
            '[ImportoQuota]',
            '[Picture]',
            '[Foto]',
		];
		
		$this->bulk_message_chunk=10;
		
	}
	
	public function get_list(){
		
		$domains=[
			'*'=>'Tutte le anagrafiche disponibili',
			'board'=>'Board',
			'memberships'=>'Soci associati',
			'status'=>'Soci',
			'customers'=>'Clienti',
			'suppliers'=>'Fornitori',
			'miscellaneous'=>'Anagrafiche generiche'
		];
		
		if($this->app->config['association']===false):
			unset($domains['board']);
			unset($domains['memberships']);
                        unset($domains['status']);
		endif;
		
		$list=[];
		
		foreach($domains as $domain=>$label):
			$item=new stdClass();
			$item->domain=$domain;
			$item->label=$label;
			$item->categories=$this->get_domain_categories($domain);
			$list[]=$item;
		endforeach;
		
		return $list;
		
	}
	
	public function get_domain_categories($domain) {
		$response=[];
        if($domain=="status") {
            // $response['1']='Associato';
            $response['2']='In attesa di rinnovo';
            $response['3']='Dimesso';
            $response['4']='Cessato';
            $response['5']='In attesa prima quota';
        } else {
            $categories=$this->db->query("select id, category from archy_registry_categories where domain='".addslashes($domain)."'");
            while($category=mysqli_fetch_object($categories)):
                $response[$category->id]=$category->category;
            endwhile;    
        }
		return $response;
	}
	
	public function get_recipients_ids($recipients){

		$domains_categories=[
			'memberships'=>'member_category_id',
			'customers'=>'customer_category_id',
			'suppliers'=>'supplier_category_id',
                        'miscellaneous'=>'category_id'
		];
                
                $domains_types = [
                    'customers'     => "type LIKE '%Cliente%'",
                    'suppliers'     => "type LIKE '%Fornitore%'",
                    'miscellaneous' => "type LIKE '%Generica%'"
                ];
		
		$domains_queries=[
			'*'=>"select id from archy_registry where email!='' ",
			'board'=>"select registry_id as id from archy_board ",
			'memberships'=>"select registry_id as id from archy_memberships where status=1",
			'status'=>"select registry_id as id from archy_memberships where 1=2",
			'customers'=>"select id from archy_registry where type like '%Cliente%' ",
			'suppliers'=>"select id from archy_registry where type like '%Fornitore%' ",
			'miscellaneous'=>"select id from archy_registry where type like '%Generica%' "
		];
		
		$queries=[];
				
		foreach($recipients as $index => $domain):

                    if(is_array($domain)):

                        foreach($domain as $category_id):

                            $category_id = (int)$category_id;

                            if($index == 'memberships'):

                                $queries[] = "
                                    SELECT registry_id AS id
                                    FROM archy_memberships
                                    WHERE status=1
                                    AND category_id=".$category_id;

                            elseif($index == 'status'):

                                $queries[] = "
                                    SELECT registry_id AS id
                                    FROM archy_memberships
                                    WHERE status=".$category_id;

                            else:

                                $queries[] = "
                                    SELECT id
                                    FROM archy_registry
                                    WHERE ".$domains_types[$index]."
                                    AND ".$domains_categories[$index]."=".$category_id;

                            endif;

                        endforeach;

                    else:

                        /*
                        Se esiste già il sotto-array categoria
                        NON eseguire la query generica
                        */
                        if (
                            isset($recipients[$domain]) &&
                            is_array($recipients[$domain])
                        ):
                            continue;
                        endif;

                        if (isset($domains_queries[$domain])):

                            $queries[] = $domains_queries[$domain];

                        endif;

                    endif;

                endforeach;

                if (empty($queries)):
                    return [];
                endif;

                $query = join(" UNION ", $queries);

        //error_log("@@@@@@@@ get_recipients_ids query: ".$query);
        //exit();
		
		$result=$this->db->query($query);
		$IDs=[];
		while($row=mysqli_fetch_object($result)):
			$IDs[]=$row->id;
		endwhile;
		
		return array_values(array_unique($IDs));

	}

    public function shortcode_replace_checkbox($p_value) {
        if(intval($p_value)==1){
                return '<input type="checkbox" name="cs" value="1" checked="checked" readonly="true"/>';
        } else {
                return '<input type="checkbox" name="cs" value="0" readonly="true"/>';
        }
    }
	
	public function get_shortcodes_replaces($registry){
		$replaces=[
			$registry->name,
			$registry->surname,
			$registry->label,
			App::latin_date($registry->birth_date),
			$registry->birth_place,
			$registry->vat_id,
			strtoupper($registry->tax_code),
			$registry->address,
			$registry->zip,
			$registry->city,
			$registry->province_district,
			$registry->province_district_code,
			$registry->state_region,
			$registry->country_it,
			$registry->country_en,
			$registry->telephone,
			$registry->fax,
			$registry->mobile,
			$registry->email,
			$registry->certified_email,
			$registry->website,
			$registry->card,
			App::latin_date($registry->membership_begin),
			App::latin_date($registry->membership_end),
			App::latin_date($registry->request_date),
			$registry->category,
            App::currency($registry->subscription_fee),
            '<img src="'.stripslashes($registry->picture).'" class="archy_img_fix_height"/>',
            '<img src="'.stripslashes($registry->main_picture).'"/>'
		];
		return $replaces;
	}
	
	public function instant_dispatch(array $options = []){
        // $media,$recipients,$subject,$message,$attachments,$page,$sender_name,$sender_mail
        $queue=new Queue();
        //var_dump($options['recipients']);exit;
		$IDs=$this->get_recipients_ids($options['recipients']);
                
		$query="
			select 
				r.*, m.card, m.membership_begin, m.membership_end, r.id as registry_id, m.id as membership_id,
				archy_cities.city, archy_cities.state_region, archy_cities.province_district_code, archy_cities.province_district,
				archy_countries.country_en, archy_countries.country_it
			from archy_registry as r 
			left join archy_memberships as m on (r.id=m.registry_id) 
			left join archy_cities on (r.city_id=archy_cities.id) 
			left join archy_countries on (archy_cities.country_id=archy_countries.id)
                        where r.id in(".join(',',$IDs).") and r.email!=''";
        //error_log("@@@@@@@@ instant_dispatch query: ".$query);
		
		$total_elements=$this->db->query($query)->num_rows;
		
		$response=new stdClass();
        
        $response->total_elements=$total_elements;
        
		if(is_array($options['attachments'])):
			$attachments_list=[];
			foreach($options['attachments'] as $file_path):
				$stream=file_get_contents($this->d_root.$file_path);
				$tmp=explode("/",$file_path);
				$file_name=end($tmp);
				$attachments_list[$file_name]=$stream;
			endforeach;
		endif;
		
		if($total_elements>0):
                        $page = $options['page'];
			$from=($this->bulk_message_chunk*$page)-$this->bulk_message_chunk;
			$pages=ceil($total_elements/$this->bulk_message_chunk);
			
			$complete_percentage=(($this->bulk_message_chunk*$page)/$total_elements)*100;
			$next_page=$pages==$page? '' : $page+1;
			
			$query.="limit ".$from.", ".$this->bulk_message_chunk;
			
			$registries=$this->db->query($query);
			while($registry=mysqli_fetch_object($registries)):
                

                               /* $replaces=$this->get_shortcodes_replaces($registry);
				$rep_subject=str_replace($this->shortcodes,$replaces,$subject);
				$rep_message=str_replace($this->shortcodes,$replaces,$message);*/
                                $rep_subject=Placeholder::load($options['subject'])->checkReplace('archy_messages',mysqli_fetch_object($this->db->query($query))); // replace placeholder
                                $rep_message=Placeholder::load($options['message'])->checkReplace('archy_messages',mysqli_fetch_object($this->db->query($query))); // replace placeholder
                        
				if($options['media']=='E-mail'):
					$params=[
						'recipient_name'=>$registry->firm_name,
						'recipient_mail'=>$registry->email,
						'sender_name'=>$options['sender_name'],
						'sender_mail'=>$options['sender_mail'],
						'subject'=>$rep_subject,
						'message'=>$rep_message
					];
					if(is_array($options['attachments'])):
						$params['attachments']=$attachments_list;
					endif;
                    // $this->email($params)? 'ok':'ko';
                    $queue->enqueue($params);
                    'ok';
				elseif($options['media']=='SMS'):
					$this->text();
				endif;
				//send message
				
			endwhile;
			
			
			$response->next_page=$next_page;
			$response->complete_percentage=$complete_percentage;
			$response->message='';
		
		else:
		
			$response->next_page='';
			$response->complete_percentage=100;
			$response->message='Nessun messaggio è stato inviato. Verifica i destinatari selezionati';
		
		endif;
		
		echo json_encode($response);
		
	}
	
	public function email($data){
		global $app;
		$settings=$app->get_settings($domain='notifications');
		$this->last_error='';
		
		$mail = new PHPMailer();
        //$mail->CharSet = 'text/html; charset=UTF-8;';
        $mail->CharSet = 'utf-8';
        //$mail->SMTPDebug = 1;
		$mail_transport=strtolower(trim((string)($app->config['mail_transport'] ?? 'auto')));
		$smtp_host=trim((string)($settings->smtp_host ?? ''));
		$smtp_username=trim((string)($settings->smtp_username ?? ''));
		$smtp_password=trim((string)($settings->smtp_password ?? ''));
		$smtp_secure=trim((string)($settings->smtp_secure ?? ''));
		$smtp_port=trim((string)($settings->smtp_port ?? ''));
		$has_smtp_credentials=$smtp_host!='' && $smtp_username!='' && $smtp_password!='';
		$has_smtp_secure_port=$smtp_secure!='' && $smtp_port!='';
		$transport_used='mail';
		
		if($mail_transport=='sendmail' && !$has_smtp_secure_port):
			$mail->isSendmail();
			$transport_used='sendmail';
		elseif($mail_transport=='smtp' || ($mail_transport=='auto' && ($has_smtp_credentials || $has_smtp_secure_port)) || (in_array($mail_transport,['mail','php_mail','sendmail']) && $has_smtp_secure_port)):
			$mail->isSMTP();
			$mail->Host = $smtp_host!=''? $smtp_host : 'localhost';
			$mail->SMTPAuth = $has_smtp_credentials;
			$mail->Username = $smtp_username;
			$mail->Password = $smtp_password;
			$mail->Timeout = 15;
			$transport_used=$mail->SMTPAuth? 'smtp-auth' : 'smtp-local';
		else:
			$mail->isMail();
		endif;
		
		if($smtp_secure!=''):
			$mail->SMTPSecure = strtolower($smtp_secure)=='starttls'? PHPMailer::ENCRYPTION_STARTTLS : strtolower($smtp_secure);
		endif;
		if($smtp_port!=''):
			$mail->Port = (int)$smtp_port;
		endif;
		if(!empty($app->config['mail_smtp_allow_self_signed'])):
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				]
			];
		endif;
		
		$mail->setFrom($data['sender_mail'],$data['sender_name']);
//		$mail->setFrom('no-reply@archy.it',$data['sender_name']);
		if(isset($data['recipient_name'])):
			$mail->addAddress($data['recipient_mail'],$data['recipient_name']);
		else:
			$mail->addAddress($data['recipient_mail']);
		endif;
		$mail->addReplyTo($data['sender_mail'],$data['sender_name']);
		
		if(isset($data['bcc'])):
			foreach($data['bcc'] as $address):
				$mail->addBCC($address);
			endforeach;
		endif;
		
		$mail->isHTML(true);
		
        $dom=new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$data['message']);
        foreach($dom->getElementsByTagName('img') as $img):
            $cid=$img->getAttribute('src');
            if(substr($cid,0,4)!="http") {
                $mail->AddEmbeddedImage($this->d_root.$cid, $cid);
                $img->setAttribute('src', 'cid:'.$cid);
            }
        endforeach;

        // Ricreo l'html dal dom, con solo il contenuto del body
        $xpath = new DOMXpath($dom);
        $innerHTML = '';
        foreach ($xpath->evaluate('//body/node()') as $node) {
            $innerHTML .= $dom->saveHtml($node);
        }
        
		$body='
			<html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <style type="text/css">
                        body{background:#FFF;color:#000;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;}
                    </style>
                </head>
                <body>
                    '.$innerHTML.'
                </body>
            </html>
        ';
        
		$mail->Subject = $data['subject'];
		$mail->Body    = $body;
		$mail->AltBody = strip_tags(str_replace("<br>","\n",preg_replace('/<style(.*?)>(.*?)<\/style>/s',"",$data['message'])));
		
		if(isset($data['attachments'])):
			foreach($data['attachments'] as $filename=>$stream):
				$mail->addStringAttachment($stream, $filename);
			endforeach;
		endif;
		
		$sent=$mail->send();
		if(!$sent):
			$this->last_error=$mail->ErrorInfo.' [transport='.$transport_used.' host='.($mail->Host?:'-').' port='.($mail->Port?:'-').' secure='.($mail->SMTPSecure?:'-').' auth='.($mail->SMTPAuth? 'yes':'no').']';
			error_log('[Message] mail failed '.$this->last_error);
		endif;
		return $sent;
	}
	
	public function test_email_credentials($recipient_address){
		$settings=$this->app->get_settings($domain='notifications');
		$email=[
			'recipient_mail'=>$recipient_address,
			'sender_name'=>$settings->sender_name,
			'sender_mail'=>$settings->sender_email,
			'subject'=>'E-mail di prova da Eteria Cloud - àèìòùé£$%&/()=?^[]{}§°ç@,;.:-_<>',
			'message'=>'E-mail di prova da Eteria Cloud - àèìòùé£$%&/()=?^[]{}§°ç@,;.:-_<>'
		];

		return $this->email($email);
		
	}
	
	public function text($data){
		
	}
	
	public function embed_images($html){ /* unused, most mail clients don't support base64 encoded images */
		$dom=new DOMDocument();
		$dom->loadHTML($html);
	
		foreach($dom->getElementsByTagName('img') as $img):
			$path=$img->getAttribute('src');
			
			$image=base64_encode(file_get_contents($this->subdomain_root.$path));
			$image_info=getimagesize($this->subdomain_root.$path);
			
			$img->setAttribute('src', 'data:'.$image_info['mime'].';base64,'.$image);
			
		endforeach;
	
		return $dom->saveHTML();
	}
	
}
?>
