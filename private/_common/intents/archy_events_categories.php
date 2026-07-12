<?php

$intent=new stdClass();
$intent->table='archy_events_categories';
$intent->title='Categorie';
$intent->singular='Categoria';
$intent->add='Nuova Categoria';
$intent->view_title='Categoria: archy_events_categories.category';
$intent->joins=array();
$intent->columns=array(
	'archy_events_categories.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events_categories.category'=>array(
		'label'=>'Categoria',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_events_categories.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_categories.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_events_categories.id','archy_events_categories.category'
);

$all_ids=array_column(mysqli_fetch_all($app->db->query("
SELECT ev.category_id AS category_id
FROM archy_events_subscriptions esu, archy_events ev
WHERE ev.id = esu.event_id 
"),MYSQLI_ASSOC),'category_id');
$intent->uneditable_ids=$all_ids;

$app->merge_intents($intent);
?>