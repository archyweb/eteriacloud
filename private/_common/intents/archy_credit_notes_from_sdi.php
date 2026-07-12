<?php
$intent=new stdClass();
$intent->table='archy_credit_notes_from_sdi';
$intent->title='Note di credito ricevute';
$intent->singular='Nota di credito ricevuta';
$intent->add='Nuova nota di credito ricevuta';
$intent->view_title='Nota di credito ricevuta: archy_credit_notes_from_sdi.number';
$intent->joins=[
	'archy_cost_centres'=>'archy_credit_notes_from_sdi.cost_centre_id=archy_cost_centres.id',
	'archy_registry'=>'archy_credit_notes_from_sdi.supplier_id=archy_registry.id'
];
$intent->columns=array(
	'archy_credit_notes_from_sdi.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_credit_notes_from_sdi.date'=>array(
		'label'=>'Data documento',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_credit_notes_from_sdi.number'=>array(
		'label'=>'Numero documento',
		'type'=>'text'
	),
	'archy_credit_notes_from_sdi.supplier_id'=>array(
		'label'=>'Fornitore',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	/*'archy_credit_notes_from_sdi.cost_centre_id'=>array(
		'label'=>'Voce di costo',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_cost_centres',
		'lookup_text'=>'archy_cost_centres.cost_centre'
	),
	'archy_credit_notes_from_sdi.type'=>array(
		'label'=>'Tipologia',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>['0'=>'Istituzionale','1'=>'Commerciale'],
		'formatter'=>function($d,$row){
			$options=['0'=>'Istituzionale','1'=>'Commerciale'];
			return $d!=''? $options[$d] : '';
		},
		'formatter_hide_in_form'=>true
	),*/
	'archy_credit_notes_from_sdi.taxable_amount'=>array(
		'label'=>'Imponibile',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_credit_notes_from_sdi.vat'=>array(
		'label'=>'Totale IVA',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_credit_notes_from_sdi.total'=>array(
		'label'=>'Totale Fattura',
		'type'=>'amount',
		'hidden'=>true,
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_credit_notes_from_sdi.paid'=>array(
		'label'=>'Totale Pagato',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes_from_sdi.unpaid'=>array(
		'label'=>'Residuo',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes_from_sdi.payment_status'=>array(
		'label'=>'Status pagamenti',
		'type'=>'select', 
		'options_type'=>'array',
		'hidden'=>'',
		'default_value'=>'not_paid',
		'options'=>['not_paid'=>'Aperta','paid'=>'Chiusa','partially_paid'=>'Parzialmente aperta'],
		'formatter'=>function($d,$row){
			$options=['not_paid'=>'Aperta','paid'=>'Chiusa','partially_paid'=>'Parzialmente aperta'];
			return @$options[$d];
		},
		'formatter_hide_in_form'=>''
	),
	'archy_credit_notes_from_sdi.invoice_id'=>array(
		'label'=>'Fattura associata',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_credit_notes_from_sdi.attachments'=>array(
		'label'=>'Documenti allegati',
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
	'archy_credit_notes_from_sdi.sdi_payload'=>array(
		'label'=>'SDI Invoice',
		'type'=>'text',
		'hidden'=>true
	),
	'archy_credit_notes_from_sdi.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_credit_notes_from_sdi.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_credit_notes_from_sdi.id',
	'archy_registry.label',
	'archy_credit_notes_from_sdi.date',
	'archy_credit_notes_from_sdi.number',
	'archy_credit_notes_from_sdi.total',
	'archy_credit_notes_from_sdi.paid',
	'archy_credit_notes_from_sdi.unpaid',
	'archy_credit_notes_from_sdi.payment_status',
	'archy_cost_centres_categories.category',
	'archy_cost_centres.cost_centre'
);
//$intent->extra_action='payable_extra_action';
$intent->include_in_form='intents/includes/sdi_credit_notes.php';
//$intent->include_in_grid='intents/includes/sdi_payables_import.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>