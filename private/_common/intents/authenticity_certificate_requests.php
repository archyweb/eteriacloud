<?php

$intent=new stdClass();
$intent->table='authenticity_certificate_requests';//nome tabella data-base
$intent->title='Richieste certificati di autenticità';//titolo (plurale)
$intent->singular='Richiesta certificato di autenticità';//titolo (singolare)
$intent->add='Nuova richiesta';//testo usato nei forms e nei pulsanti
$intent->view_title='Richiesta certificato di autenticità: authenticity_certificate_requests.name authenticity_certificate_requests.surname';
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'authenticity_certificate_requests.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'authenticity_certificate_requests.status'=>array(
		'label'=>'Status',
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'Da autenticare',
			'1'=>'Opera autentica',
			'2'=>'Opera non autentica'
		),
		'formatter'=>function($d,$row){
			$values=array(
				'0'=>'Da autenticare',
				'1'=>'Opera autentica',
				'2'=>'Opera non autentica'
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'authenticity_certificate_requests.name'=>array(
		'label'=>'Nome',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'authenticity_certificate_requests.surname'=>array(
		'label'=>'Cognome',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'authenticity_certificate_requests.address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'authenticity_certificate_requests.city'=>array(
		'label'=>'Città',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'authenticity_certificate_requests.zip'=>array(
		'label'=>'CAP',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'authenticity_certificate_requests.phone'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'authenticity_certificate_requests.fax'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		'required'=>true,
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	
	'authenticity_certificate_requests.certificate_name'=>array(
		'label'=>'Nome su certificato',
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'1'=>'Città, nome, cognome',
			'2'=>'Città, "collezione privata"',
			'3'=>'"collezione privata"',
			'4'=>'Come specificato'
		),
		'formatter'=>function($d,$row){
			$values=array(
				'1'=>'Città, nome, cognome',
				'2'=>'Città, "collezione privata"',
				'3'=>'"collezione privata"',
				'4'=>'Come specificato'
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'authenticity_certificate_requests.certificate_custom_name'=>array(
		'label'=>'Nome su certificato specifico',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.catalog_name'=>array(
		'label'=>'Nome su catalogo',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>array(
			'1'=>'Città, nome, cognome',
			'2'=>'Città, "collezione privata"',
			'3'=>'"collezione privata"',
			'4'=>'Come specificato'
		),
		'formatter'=>function($d,$row){
			$values=array(
				'1'=>'Città, nome, cognome',
				'2'=>'Città, "collezione privata"',
				'3'=>'"collezione privata"',
				'4'=>'Come specificato'
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'authenticity_certificate_requests.catalog_custom_name'=>array(
		'label'=>'Nome su catalogo specifico',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'authenticity_certificate_requests.title'=>array(/**/
		'label'=>"Titolo dell'opera",
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.technique'=>array(
		'label'=>'Tecnica',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.support'=>array(
		'label'=>'Supporto',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.no_frame_height'=>array(
		'label'=>'Altezza senza cornice',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.no_frame_width'=>array(
		'label'=>'Larghezza senza cornice',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.no_frame_depth'=>array(
		'label'=>'Profondità senza cornice',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.no_frame_diameter'=>array(
		'label'=>'Diametro senza cornice',
		'type'=>'text',
		'hidden'=>''
	),
	
	'authenticity_certificate_requests.frame_height'=>array(
		'label'=>'Altezza con cornice',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.frame_width'=>array(
		'label'=>'Larghezza con cornice',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.frame_depth'=>array(
		'label'=>'Profondità con cornice',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.frame_diameter'=>array(
		'label'=>'Diametro con cornice',
		'type'=>'text',
		'hidden'=>''
	),
	
	'authenticity_certificate_requests.title_position'=>array(
		'label'=>'Posizione del titolo',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$positions_data=[
				'front'=>'fronte',
				'back'=>'retro',
				'up'=>'in alto',
				'down'=>'in basso',
				'left'=>'a sinistra',
				'right'=>'a destra',
				'center'=>'al centro'
			];
			$positions=unserialize($d);
			$pos=[];
			if(is_array($positions)):
				foreach($positions as $position):
					$pos[]=$positions_data[$position];
				endforeach;
			endif;
			return join(', ',$pos);
		},
		'hidden'=>''
	),	
	'authenticity_certificate_requests.title_description'=>array(
		'label'=>'Descrizione titolo',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.title_file'=>array(
		'label'=>'Immagine titolo',
		'type'=>'file',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true
	),
	'authenticity_certificate_requests.date_position'=>array(
		'label'=>'Posizione della data',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$positions_data=[
				'front'=>'fronte',
				'back'=>'retro',
				'up'=>'in alto',
				'down'=>'in basso',
				'left'=>'a sinistra',
				'right'=>'a destra',
				'center'=>'al centro'
			];
			$positions=unserialize($d);
			$pos=[];
			if(is_array($positions)):
				foreach($positions as $position):
					$pos[]=$positions_data[$position];
				endforeach;
			endif;
			return join(', ',$pos);
		},
		'hidden'=>''
	),	
	'authenticity_certificate_requests.date_description'=>array(
		'label'=>'Descrizione della data',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'hidden'=>''
	),
	'authenticity_certificate_requests.date_file'=>array(
		'label'=>'Immagine della data',
		'type'=>'file',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true
	),
	'authenticity_certificate_requests.sign_position'=>array(
		'label'=>'Posizione della firma',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$positions_data=[
				'front'=>'fronte',
				'back'=>'retro',
				'up'=>'in alto',
				'down'=>'in basso',
				'left'=>'a sinistra',
				'right'=>'a destra',
				'center'=>'al centro'
			];
			$positions=unserialize($d);
			$pos=[];
			if(is_array($positions)):
				foreach($positions as $position):
					$pos[]=$positions_data[$position];
				endforeach;
			endif;
			return join(', ',$pos);
		},
		'hidden'=>''
	),	
	'authenticity_certificate_requests.sign_description'=>array(
		'label'=>'Descrizione della firma',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.sign_file'=>array(
		'label'=>'Immagine della firma',
		'type'=>'file',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true
	),
	'authenticity_certificate_requests.text_position'=>array(
		'label'=>'Posizione di dediche o altre scritte',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$positions_data=[
				'front'=>'fronte',
				'back'=>'retro',
				'up'=>'in alto',
				'down'=>'in basso',
				'left'=>'a sinistra',
				'right'=>'a destra',
				'center'=>'al centro'
			];
			$positions=unserialize($d);
			$pos=[];
			if(is_array($positions)):
				foreach($positions as $position):
					$pos[]=$positions_data[$position];
				endforeach;
			endif;
			return join(', ',$pos);
		},
		'hidden'=>''
	),	
	'authenticity_certificate_requests.text_description'=>array(
		'label'=>'Descrizione di dediche o altre scritte',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.text_file'=>array(
		'label'=>'Immagine delle dediche/scritte',
		'type'=>'file',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true
	),
	'authenticity_certificate_requests.label_position'=>array(
		'label'=>'Posizione etichette',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$positions_data=[
				'front'=>'fronte',
				'back'=>'retro',
				'up'=>'in alto',
				'down'=>'in basso',
				'left'=>'a sinistra',
				'right'=>'a destra',
				'center'=>'al centro'
			];
			$positions=unserialize($d);
			$pos=[];
			if(is_array($positions)):
				foreach($positions as $position):
					$pos[]=$positions_data[$position];
				endforeach;
			endif;
			return join(', ',$pos);
		},
		'hidden'=>''
	),	
	'authenticity_certificate_requests.label_description'=>array(
		'label'=>'Descrizione etichette',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.label_file'=>array(
		'label'=>'Immagine delle etichette',
		'type'=>'file',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true
	),
	'authenticity_certificate_requests.stamp_position'=>array(
		'label'=>'Posizione timbri',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$positions_data=[
				'front'=>'fronte',
				'back'=>'retro',
				'up'=>'in alto',
				'down'=>'in basso',
				'left'=>'a sinistra',
				'right'=>'a destra',
				'center'=>'al centro'
			];
			$positions=unserialize($d);
			$pos=[];
			if(is_array($positions)):
				foreach($positions as $position):
					$pos[]=$positions_data[$position];
				endforeach;
			endif;
			return join(', ',$pos);
		},
		'hidden'=>''
	),	
	'authenticity_certificate_requests.stamp_description'=>array(
		'label'=>'Descrizione timbri',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.stamp_file'=>array(
		'label'=>'Immagine dei timbri',
		'type'=>'file',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true
	),
	
	'authenticity_certificate_requests.inventory_number'=>array(
		'label'=>'N° inventario',
		'type'=>'text',
		'hidden'=>''
	),
	'authenticity_certificate_requests.purchase_date'=>array(
		'label'=>'Data di acquisto',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
	
	'authenticity_certificate_requests.provenance'=>array(
		'label'=>'Provenienza opera',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.era_value'=>array(
		'label'=>'Valore d\'epoca',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.autenticity_certificates'=>array(
		'label'=>'Certificati di autenticità',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.auth_files'=>array(
		'label'=>'Certificati di autenticità',
		'type'=>'bulk_upload',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			$paths=unserialize($d);
			$return='<ol>';
			if(is_array($paths)):
				foreach($paths as $path):
					$temp=explode('/',$path);
					$filename=end($temp);
					$return.='<li><a href="'.$path.'" download>'.$filename.'</a></li>';
				endforeach;
			endif;
			$return.='</ol>';
			return $return;
		},
		'formatter_hide_in_form'=>true
	),
	'authenticity_certificate_requests.state_of_preservation'=>array(
		'label'=>'Stato di conservazione',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.restorations'=>array(
		'label'=>'Opere di restauro',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.expositions'=>array(
		'label'=>'Esposizioni',
		'type'=>'editor',
		'hidden'=>''
	),
	'authenticity_certificate_requests.bibliography'=>array(
		'label'=>'Bibliografia',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'authenticity_certificate_requests.attachments'=>array(
		'label'=>'Documentazione',
		'type'=>'bulk_upload',
		'formatter'=>function($d,$row){
			$paths=unserialize($d);
			$return='<ol>';
			foreach($paths as $path):
				$temp=explode('/',$path);
				$filename=end($temp);
				$return.='<li><a href="'.$path.'" download>'.$filename.'</a></li>';
			endforeach;
			$return.='</ol>';
			return $return;
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
		
	'authenticity_certificate_requests.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'authenticity_certificate_requests.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'authenticity_certificate_requests.id',
	'authenticity_certificate_requests.status',
	'authenticity_certificate_requests.name',
	'authenticity_certificate_requests.surname',
	'authenticity_certificate_requests.city',
	'authenticity_certificate_requests.email'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->extra_buttons=function($id,$row){
	global $app;
	$pdf_path=$app->abs_root.'assets/documents/auth_certificates_requests/'.$id.'.pdf';
	$extra_button=' <a class="btn btn-sm btn-success" href="'.$pdf_path.'" download data-toggle="tooltip" title="Richiesta originaria"><i class="glyphicon glyphicon-file"></i></a>';
	return $extra_button;
};

$intent->include_in_view='intents/includes/mark_as_read.php';
$intent->include_in_form='intents/includes/authenticity_certificate_requests.php';
$intent->extra_action='create_authenticity_certificate';

$app->merge_intents($intent);
?>