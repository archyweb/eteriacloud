<?php
$intent=new stdClass();
$intent->table='archy_cash_budget';
$intent->title='Bilancio di Cassa';
$intent->singular='Bilancio di Cassa';
$intent->add='Nuova voce';
$intent->view_title='Bilancio di Cassa del archy_cash_budget.date - archy_petty_cash_purposes.purpose';
$intent->joins=array(
	'archy_petty_cash_purposes'=>'archy_cash_budget.purpose_id=archy_petty_cash_purposes.id'
);
$intent->columns=array(
	'archy_cash_budget.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_cash_budget.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'required'=>true,
		'default_value'=>date('d/m/Y')
	),
	'archy_cash_budget.purpose_id'=>array(
		'label'=>'Causale',
		'type'=>'search',
		'intent'=>'archy_petty_cash_purposes',
		'lookup_text'=>'purpose',
		'required'=>true
	),
	'archy_cash_budget.amount'=>array(
		'label'=>'Importo',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>'',
		'required'=>true
	),
	'archy_cash_budget.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_cash_budget.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_cash_budget.id',
	'archy_cash_budget.date',
	'archy_petty_cash_purposes.purpose',
	'archy_cash_budget.amount'
);
$intent->include_in_grid='intents/includes/cash_budget.php';
$app->merge_intents($intent);
?>