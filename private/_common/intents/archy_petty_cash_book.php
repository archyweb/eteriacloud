<?php
$intent=new stdClass();
$intent->table='archy_petty_cash_book';
$intent->title='Registrazioni contabili';
$intent->singular='Registrazione contabile';
$intent->add='Nuova voce';
$intent->view_title='Registrazione contabile del archy_petty_cash_book.date - archy_petty_cash_book.petty_cash';
$intent->joins=array(
	'archy_petty_cash_types'=>'archy_petty_cash_book.petty_cash_type_id=archy_petty_cash_types.id',
	'archy_cost_centres'=>'archy_petty_cash_book.petty_cash_purpose_id=archy_cost_centres.id'//use old column name petty_cash_purpose_id instead of cost_centre_id 
	//'archy_petty_cash_purposes'=>'archy_petty_cash_book.petty_cash_purpose_id=archy_petty_cash_purposes.id'
);

$intent->columns=array(
	'archy_petty_cash_book.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_petty_cash_book.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'required'=>true,
		'default_value'=>date('d/m/Y')
	),
//	'archy_petty_cash_book.petty_cash_purpose_id'=>array(
//		'label'=>'Causale',
//		'type'=>'search',
//		'intent'=>'archy_petty_cash_purposes',
//		'lookup_text'=>'purpose',
//		'required'=>true
//	),
	'archy_petty_cash_book.petty_cash_purpose_id'=>array(
		'label'=>'Voce contabile',
		'type'=>'search',
		'intent'=>'archy_cost_centres',
		'lookup_text'=>'cost_centre'
	),
	'archy_petty_cash_book.petty_cash'=>array(
		'label'=>'Descrizione',
		'type'=>'text',
		'required'=>true
	),
	'archy_petty_cash_book.petty_cash_type_id'=>array(
		'label'=>'Libro',
		'type'=>'search',
		'intent'=>'archy_petty_cash_types',
		'lookup_text'=>'type',
		'required'=>true
	),
	'archy_petty_cash_book.amount'=>array(
		'label'=>'Importo',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>'',
		'required'=>true
	),
	'archy_petty_cash_book.type'=>array(
		'label'=>'Tipologia',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>['0'=>'Istituzionale','1'=>'Commerciale'],
		'formatter'=>function($d,$row){
			$options=['0'=>'Istituzionale','1'=>'Commerciale'];
			return $d!=''? $options[$d] : '';
		},
		'formatter_hide_in_form'=>true
	),
	'archy_petty_cash_book.bulk_payment'=>array(
		'label'=>'Pagamento cumulativo',
        'type'=>'checkbox',
        'default_value'=>'0',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
    ),
	'archy_petty_cash_book.attachment'=>array(
		'label'=>'Allegati',
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
		'formatter_hide_in_form'=>true
	),
	'archy_petty_cash_book.document_table'=>array(
		'label'=>'Documento associato',
		'type'=>'text',
		'formatter'=>function($d,$row){
			$formatter=['archy_invoices'=>'Fattura','archy_credit_notes'=>'Nota di credito','archy_tax_receipts'=>'Ricevuta','archy_passive_receipts'=>'Storno ricevuta','archy_payables'=>'Documento di acquisto']; 
			return $formatter[$d];
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_petty_cash_book.document_id'=>array(
		'label'=>'ID Documento associato',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_petty_cash_book.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_petty_cash_book.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
if($app->config['association']==false):
	unset($intent->columns['archy_petty_cash_book.type']);
endif;
$intent->default_columns=array(
	'archy_petty_cash_book.id',
	'archy_petty_cash_book.date',
	//'archy_petty_cash_purposes.purpose',
	'archy_cost_centres.cost_centre',
	'archy_petty_cash_book.petty_cash',
	'archy_petty_cash_book.amount',
	'archy_petty_cash_book.attachment'
);
$intent->include_in_grid='intents/includes/petty_cash_balance.php';
$intent->include_in_form='intents/includes/petty_cash_book.php';
$intent->extra_action='petty_cash_book';
$intent->before_delete='&before_delete=petty_cash_book_before_delete';
$intent->after_delete='&after_delete=petty_cash_book_after_delete';
$intent->before_update='petty_cash_book_before_update';
$intent->after_update='petty_cash_book_after_update';

$app->merge_intents($intent);
?>