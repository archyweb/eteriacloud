<?php

$intent=new stdClass();
$intent->table='archy_events_speakers';
$intent->title='Relatori';
$intent->singular='Relatore';
$intent->add='Nuovo relatore';
$intent->view_title='archy_events_speakers.name';
$intent->joins=[];
$intent->columns=array(
	'archy_events_speakers.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events_speakers.name'=>array(
		'label'=>'Nome completo',
		'type'=>'text',
		'required'=>true,
		//'hidden'=>'',
		'grid_search_type'=>'search'
	),
	'archy_events_speakers.picture'=>array(
		'label'=>'Foto',
		'type'=>'file',
		'formatter'=>function($d,$row){
			return $d!=''? 
				'<a href="'.$d.'" download><img src="'.$d.'" alt="Foto" class="tt" title="Download" style="max-height:44px"></a>':
				'';
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		//'hidden'=>''
	),
	'archy_events_speakers.telephone'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		//'hidden'=>''
	),
	'archy_events_speakers.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		//'hidden'=>''
	),
    'archy_events_speakers.website'=>array(
		'label'=>'Sito web',
		'type'=>'text',
		//'hidden'=>''
	),
	'archy_events_speakers.bio'=>array(
		'label'=>'Biografia',
		'type'=>'editor',
		//'editor_height'=>200,
		//'hidden'=>''
	),
	'archy_events_speakers.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_speakers.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_events_speakers.id',
	'archy_events_speakers.name',
	'archy_events_speakers.telephone',
	'archy_events_speakers.email'
);

// Lo speaker non va più modificato/cancellato se ha iscritti
$all_speakers=mysqli_fetch_all($app->db->query("SELECT DISTINCT esp.speakers AS speaker FROM archy_events_sessions ese, archy_events_subscriptions esu, archy_events_speeches esp WHERE ese.event_id = esu.event_id and esp.session_id = ese.id"),MYSQLI_ASSOC);
$all_ids=[];
foreach($all_speakers as $el) {
    $v=unserialize($el['speaker']);
    $all_ids=array_merge($all_ids,$v);
}
$intent->uneditable_ids=$all_ids;

$app->merge_intents($intent);
?>