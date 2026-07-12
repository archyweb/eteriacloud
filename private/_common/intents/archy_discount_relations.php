<?php

$intent=new stdClass();
$intent->table='archy_discount_relations';
$intent->title='Abbinamento rating/sconti';
$intent->singular='Sconto rating';
$intent->add='Nuovo abbinamento sconto';
$intent->view_title='Relazioni sconti archy_ratings.title';
$intent->joins=[
    'archy_ratings'=>'archy_discount_relations.rating_id=archy_ratings.id',
    'archy_discounts'=>'archy_discount_relations.discount_id=archy_discounts.id',
];
$intent->columns=array(
	'archy_discount_relations.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_discount_relations.rating_id'=>array(
		'label'=>'Rating',
		'type'=>'search',
		'intent'=>'archy_ratings',
		'lookup_text'=>'archy_ratings.title',
		'required'=>true
	),
    'archy_discount_relations.discount_id'=>array(
		'label'=>'Sconti',
		'type'=>'search',
		'intent'=>'archy_discounts',
		'lookup_text'=>'archy_discounts.title',
		'required'=>true
	),
    'archy_discount_relations.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_discount_relations.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->noinsert=true; // Serve per togliere + nella view (lentina)

$intent->default_columns=array(
	'archy_discount_relations.id',
    'archy_ratings.title',
    'archy_discounts.title',
);

$app->merge_intents($intent);
