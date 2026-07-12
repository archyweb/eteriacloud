<?php
$defaults=$app->get_settings($domain='association_1');
$intent=new stdClass();
$intent->table='archy_meetings_book';//nome tabella data-base
$intent->title='Libro Adunanze';//titolo (plurale)
$intent->singular='Adunanza';//titolo (singolare)
$intent->add='Nuova Adunanza';//testo usato nei forms e nei pulsanti
$intent->view_title='Adunanza: archy_meetings_book.title';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=['archy_meeting_categories'=>'archy_meetings_book.meeting_category_id=archy_meeting_categories.id'];
$intent->columns=array(//colonne della tabella principale
	'archy_meetings_book.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_meetings_book.title'=>array(
		'label'=>'Titolo adunanza',
		'type'=>'text',
		'required'=>true,
		'default_value'=>@$defaults->meeting_title,
		'hidden'=>''
	),
	/*'archy_meetings_book.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),*/
	'archy_meetings_book.date_time'=>array(
		'label'=>'Data e Orario',
		'type'=>'datetime',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_meetings_book.meeting_category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_meeting_categories',
		'lookup_text'=>'archy_meeting_categories.category',
		'required'=>true,
		'grid_search_type'=>'search',
		'hidden'=>''
	),
	'archy_meetings_book.president'=>array(
		'label'=>'Presidente',
		'type'=>'text',
		'required'=>true,
		'default_value'=>@$defaults->meeting_president,
		'hidden'=>''
	),
	'archy_meetings_book.secretary'=>array(
		'label'=>'Segretario',
		'type'=>'text',
		'required'=>true,
		'default_value'=>@$defaults->meeting_secretary,
		'hidden'=>''
	),
	'archy_meetings_book.place'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'required'=>true,
		'default_value'=>@$defaults->meeting_place,
		'hidden'=>''
	),
	'archy_meetings_book.signed_report'=>array(
		'label'=>'Verbale firmato',
		'type'=>'bulk_upload',
		'formatter'=>function($d,$row){
			$paths=unserialize($d);
			$return='<ol>';
			if(!$paths) $paths=[];
			foreach($paths as $path):
				$temp=explode('/',$path);
				$filename=end($temp);
				$return.='<li><a href="'.$path.'" download>'.$filename.'</a></li>';
			endforeach;
			$return.='</ol>';
			return $return;
        },
        'excel_formatter'=>function($d,$row){
			$paths=unserialize($d);
			$return='';
			if(!$paths) $paths=[];
			foreach($paths as $path):
				$temp=explode('/',$path);
				$filename=end($temp);
				$return.='https://'.$_SERVER['HTTP_HOST'].$path.' ';
			endforeach;
			return $return;
        },
        'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_meetings_book.unsigned_report'=>array(
		'label'=>'Verbale',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'editor_purpose'=>'meeting',
		'hidden'=>''
	),
	'archy_meetings_book.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_meetings_book.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_meetings_book.id','archy_meetings_book.title','archy_meetings_book.date_time','archy_meeting_categories.category','archy_meetings_book.signed_report'
);

$intent->extra_buttons=function($id,$row){
	
	global $app;
	
	$extra_buttons='<span style="display:inline-block;width:1px;padding:0 4px;"></span>';
	
	/*$accepted_candidates='assets/documents/meetings_book/'.$id.'/candidati_accettati.pdf';
	if(is_file($app->subdomain_root.'/'.$accepted_candidates)):
		$extra_buttons.=' <a class="btn btn-sm btn-success" href="'.$app->subdomain_root.'/'.$accepted_candidates.'" data-toggle="tooltip" title="Candidati accettati"><i class="glyphicon glyphicon-user"></i></a>';
	endif;*/
	
	/*$refused_candidates='assets/documents/meetings_book/'.$id.'/candidati_respinti.pdf';
	if(is_file($app->subdomain_root.'/'.$refused_candidates)):
		$extra_buttons.=' <a class="btn btn-sm btn-danger" href="/'.$refused_candidates.'" data-toggle="tooltip" title="Candidati respinti"><i class="glyphicon glyphicon-user"></i></a>';
	endif;*/
	
	$unsigned_report='assets/documents/meetings_book/'.$id.'/verbale_da_firmare.pdf';
	if(is_file($app->subdomain_root.'/'.$unsigned_report)):
		$extra_buttons.=' <a class="btn btn-outline-secondary" href="/'.$unsigned_report.'" data-toggle="tooltip" title="Verbale da Firmare"><span class="far fa-file-archive"></span></a>';
	endif;
	
	return $extra_buttons;
	
};

$intent->include_in_form='intents/includes/meetings.php';
$intent->extra_action='board_meeting';
$app->merge_intents($intent);
?>