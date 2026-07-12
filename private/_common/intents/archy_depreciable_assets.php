<?php
$intent=new stdClass();
$intent->table='archy_depreciable_assets';
$intent->title='Registro beni ammortizzabili';
$intent->singular='Bene ammortizzabile';
$intent->add='Nuovo bene ammortizzabile';
$intent->view_title='Bene ammortizzabile: archy_depreciable_assets.asset_name';
$intent->joins=[];
$intent->columns=array(
	'archy_depreciable_assets.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_depreciable_assets.type'=>array(
		'label'=>'Immobilizazzione',
		'type'=>'select', 
		'default_value'=>'0',
		'options_type'=>'array',
		'options'=>['0'=>'Materiale','1'=>'Immateriale','2'=>'Finanziaria'],
		'formatter'=>function($d,$row){
			$options=['0'=>'Materiale','1'=>'Immateriale','2'=>'Finanziaria'];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true
	),
	'archy_depreciable_assets.asset_name'=>array(
		'label'=>'Nome del bene',
		'type'=>'text',
		'required'=>true
	),
	'archy_depreciable_assets.purchase_date'=>array(
		'label'=>'Data di acquisto',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'required'=>true
	),
	'archy_depreciable_assets.purchase_price'=>array(
		'label'=>'Prezzo di acquisto',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'required'=>true
	),
	'archy_depreciable_assets.asset_life'=>array(
		'label'=>'Vita utile (anni)',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'required'=>true
	),
	'archy_depreciable_assets.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_depreciable_assets.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_depreciable_assets.id',
	'archy_depreciable_assets.type',
	'archy_depreciable_assets.asset_name',
	'archy_depreciable_assets.purchase_date',
	'archy_depreciable_assets.purchase_price',
	'archy_depreciable_assets.asset_life'
);
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>