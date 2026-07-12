<?php
$intent=new stdClass();
$intent->table='archy_payables';
$intent->title='Documenti di acquisto';
$intent->singular='Documento di acquisto';
$intent->add='Nuovo documento di acquisto';
$intent->view_title='Documento di acquisto: archy_payables.number';
$intent->joins=[
	//'archy_cost_centres'=>'archy_payables.cost_centre_id=archy_cost_centres.id',
	'archy_registry'=>'archy_payables.supplier_id=archy_registry.id'
];
$intent->columns=array(
	'archy_payables.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_payables.date'=>array(
		'label'=>'Data fattura',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_payables.number'=>array(
		'label'=>'Numero documento',
		'type'=>'text'
	),
	'archy_payables.supplier_id'=>array(
		'label'=>'Fornitore',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	/*'archy_payables.cost_centre_id'=>array(
		'label'=>'Voce di costo',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_cost_centres',
		'lookup_text'=>'archy_cost_centres.cost_centre'
	),*/
	'archy_payables.type'=>array(
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
	'archy_payables.taxable_amount'=>array(
		'label'=>'Imponibile Fattura',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_payables.vat'=>array(
		'label'=>'Totale IVA',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_payables.expiration_date'=>array(
		'label'=>'Scadenza',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);}
	),
	'archy_payables.total'=>array(
		'label'=>'Totale Fattura',
		'type'=>'amount',
		'hidden'=>true,
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_payables.paid'=>array(
        'label'=>'Totale Pagato',
        'type'=>'amount',
        'hidden'=>true,
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
	),
	'archy_payables.unpaid'=>array(
		'label'=>'Residuo',
        'type'=>'amount',
        'hidden'=>true,
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_payables.payment_status'=>array(
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
	'archy_payables.attachments'=>array(
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
	'archy_payables.sdi_invoice'=>array(
		'label'=>'SDI Invoice',
		'type'=>'text',
		'hidden'=>true
	),
	'archy_payables.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_payables.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_payables.id',
	'archy_registry.label',
	'archy_payables.date',
	'archy_payables.number',
	'archy_payables.total',
	'archy_payables.paid',
	'archy_payables.unpaid',
	'archy_payables.payment_status',
	//'archy_cost_centres_categories.category',
	//'archy_cost_centres.cost_centre'
);
if($app->config['association']==false):
	unset($intent->columns['archy_payables.type']);
endif;

$intent->undeletable_ids=array_column(mysqli_fetch_all($app->db->query("select id from archy_payables where sdi_invoice!=''"),MYSQLI_ASSOC),'id');

$intent->extra_action='payable_extra_action';
$intent->include_in_form='intents/includes/sdi_payables.php';
$intent->include_in_grid='intents/includes/sdi_payables_import.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
