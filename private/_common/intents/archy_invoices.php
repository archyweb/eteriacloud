<?php

$intent=new stdClass();
$intent->table='archy_invoices';
$intent->title='Fatture';
$intent->singular='Fattura';
$intent->add='Nuova Fattura';
$intent->view_title='Fattura archy_invoices.number del archy_invoices.date';
$intent->help=array(
    'edit'=>"
<h3>Help Fatture (edit)</h3>
<p>bla bla bla</p>
<p>bla bla bla</p>",
    'grid'=>"
<h3>Help Fatture (griglia)</h3>
<p>bla bla bla</p>
<p>bla bla bla</p>"
); // vedere echo in edit.php e grid.php

$intent->joins=array(
	'archy_firms'=>'archy_invoices.firm_id=archy_firms.id',
	'archy_registry'=>'archy_invoices.customer_id=archy_registry.id',
	'archy_payment_methods'=>'archy_invoices.payment_method_id=archy_payment_methods.id',
    'archy_banks'=>'archy_invoices.bank_id=archy_banks.id'
);

$intent->columns=array(
	'archy_invoices.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_invoices.document_type'=>array(
		'label'=>'Tipo di documento',
		'type'=>'select', 
		'default_value'=>'TD01',
		'options_type'=>'array',
		//'TD04'=>'Nota di Credito',
		'options'=>[
            'TD01'=>'Fattura',
            'TD02'=>'Acconto/Anticipo su Fattura',
            'TD03'=>'Acconto/Anticipo su Parcella',
			'TD04'=>'Nota di Credito',
            'TD05'=>'Nota di Debito',
            'TD06'=>'Parcella',
            'TD07'=>'Fattura semplificata: da utilizzare in presenza di fattura semplificata di cui all’art. 21-bis del DPR n. 633/1972',
            'TD08'=>'Nota di Credito semplificata: da utilizzare in caso di utilizzo di nota credito semplificata',
            'TD09'=>'Nota di debito semplificata',
            'TD10'=>'Fattura di acquisto intracomunitario beni',
            'TD11'=>'Fattura di acquisto intracomunitario servizi',
            'TD12'=>'Documento riepilogativo (art. 6, d.P.R 695/1996)',
			'TD16'=>'Integrazione fattura reverse charge interno (di nuova istituzione): da utilizzare in presenza di reverse charge interno, per meglio dire le casistiche di cui all’art. 17, commi 5 e 6 del DPR n. 633/1972 (ad es. servizi nel subappalto edile, ecc.) e art. 74 del DPR n. 633/1972 (rottami-cascami)',
            'TD17'=>'Integrazione/autofattura per acquisto servizi dall’estero (di nuova istituzione): da utilizzare per acquisti di servizi territorialmente rilevanti ai fi ni IVA in Italia qualora il fornitore sia un soggetto passivo stabilito ai fi ni IVA in altro Paese della UE ovvero in un Paese extra-UE',
            'TD18'=>'Integrazione per acquisto di beni intracomunitari (di nuova istituzione): da utilizzare in presenza di acquisti intracomunitari di beni di cui all’art. 38 del DL n. 331/1993',
            'TD19'=>'Integrazione/autofattura per acquisto di beni ex art. 17 c. 2 DPR n. 633/1972 (di nuova istituzione): da utilizzare in presenza di cessioni di beni effettuate nel territorio dello Stato da soggetti non residenti',
            'TD20'=>'Autofattura per regolarizzazione e integrazione delle fatture (art. 6 c. 8 D.Lgs. n. 471/1997 o art. 46 c. 5 DL n. 331/1993) (Caso del mancato ricevimento della fattura entro quattro mesi) (già presente ma ampliata la descrizione)',
            'TD21'=>'Autofattura per splafonamento (di nuova istituzione): da utilizzare al fine di regolarizzare lo splafonamento IVA',
            'TD22'=>'Estrazione beni da Deposito IVA (di nuova istituzione)',
            'TD23'=>'Estrazione beni da Deposito IVA con versamento dell’IVA (di nuova istituzione)',
            'TD24'=>'Fattura differita di cui all’art. 21, comma 4, lett. a) (di nuova istituzione)',
            'TD25'=>'Fattura differita di cui all’art. 21, comma 4, terzo periodo lett. b) (di nuova istituzione)',
            'TD26'=>'Cessione di beni ammortizzabili e per passaggi interni (ex art. 36 DPR n. 633/1972) (di nuova istituzione)',
            'TD27'=>'Fattura per autoconsumo o per cessioni gratuite senza rivalsa (di nuova istituzione)',
			'TD28'=>'acquisti da San Marino con IVA (fattura cartacea)',
            'TD29'=>'comunicazione per omessa o irregolare fatturazione (ex art. 6, comma 8, d.lgs 471/197)'
        ],
		'formatter'=>function($d,$row){
			$options=[
                'TD01'=>'Fattura',
                'TD02'=>'Acconto/Anticipo su Fattura',
                'TD03'=>'Acconto/Anticipo su Parcella',
				'TD04'=>'Nota di Credito',
                'TD05'=>'Nota di Debito',
                'TD06'=>'Parcella',
                'TD07'=>'Fattura semplificata: da utilizzare in presenza di fattura semplificata di cui all’art. 21-bis del DPR n. 633/1972',
                'TD08'=>'Nota di Credito semplificata: da utilizzare in caso di utilizzo di nota credito semplificata',
                'TD09'=>'Nota di debito semplificata',
				'TD10'=>'Fattura di acquisto intracomunitario beni',
				'TD11'=>'Fattura di acquisto intracomunitario servizi',
				'TD12'=>'Documento riepilogativo (art. 6, d.P.R 695/1996)',
				'TD16'=>'Integrazione fattura reverse charge interno (di nuova istituzione): da utilizzare in presenza di reverse charge interno, per meglio dire le casistiche di cui all’art. 17, commi 5 e 6 del DPR n. 633/1972 (ad es. servizi nel subappalto edile, ecc.) e art. 74 del DPR n. 633/1972 (rottami-cascami)',
                'TD17'=>'Integrazione/autofattura per acquisto servizi dall’estero (di nuova istituzione): da utilizzare per acquisti di servizi territorialmente rilevanti ai fi ni IVA in Italia qualora il fornitore sia un soggetto passivo stabilito ai fi ni IVA in altro Paese della UE ovvero in un Paese extra-UE',
                'TD18'=>'Integrazione per acquisto di beni intracomunitari (di nuova istituzione): da utilizzare in presenza di acquisti intracomunitari di beni di cui all’art. 38 del DL n. 331/1993',
                'TD19'=>'Integrazione/autofattura per acquisto di beni ex art. 17 c. 2 DPR n. 633/1972 (di nuova istituzione): da utilizzare in presenza di cessioni di beni effettuate nel territorio dello Stato da soggetti non residenti',
                'TD20'=>'Autofattura per regolarizzazione e integrazione delle fatture (art. 6 c. 8 D.Lgs. n. 471/1997 o art. 46 c. 5 DL n. 331/1993) (Caso del mancato ricevimento della fattura entro quattro mesi) (già presente ma ampliata la descrizione)',
                'TD21'=>'Autofattura per splafonamento (di nuova istituzione): da utilizzare al fine di regolarizzare lo splafonamento IVA',
                'TD22'=>'Estrazione beni da Deposito IVA (di nuova istituzione)',
                'TD23'=>'Estrazione beni da Deposito IVA con versamento dell’IVA (di nuova istituzione)',
                'TD24'=>'Fattura differita di cui all’art. 21, comma 4, lett. a) (di nuova istituzione)',
                'TD25'=>'Fattura differita di cui all’art. 21, comma 4, terzo periodo lett. b) (di nuova istituzione)',
                'TD26'=>'Cessione di beni ammortizzabili e per passaggi interni (ex art. 36 DPR n. 633/1972) (di nuova istituzione)',
                'TD27'=>'Fattura per autoconsumo o per cessioni gratuite senza rivalsa (di nuova istituzione)',
				'TD28'=>'acquisti da San Marino con IVA (fattura cartacea)',
				'TD29'=>'comunicazione per omessa o irregolare fatturazione (ex art. 6, comma 8, d.lgs 471/197)'
            ];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true
	),
	'archy_invoices.VAT_collectability'=>array(
		'label'=>'Esigibilità IVA',
		'type'=>'select', 
		'default_value'=>$app->default_VAT_collectability,
		'options_type'=>'array',
		'options'=>['I'=>'IVA ad esigibilità immediata', 'D'=>'IVA ad esigibilità differita', 'S'=>'Scissione dei pagamenti'],
		'formatter'=>function($d,$row){
			$collectability=['I'=>'IVA ad esigibilità immediata', 'D'=>'IVA ad esigibilità differita', 'S'=>'Scissione dei pagamenti'];
			return $collectability[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true
	),
	'archy_invoices.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id,
		'help'=>"Questo campo serve per selezionare l'Azienda o il registro sezionale"
	),
	'archy_invoices.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_invoices.number'=>array(
		'label'=>'Numero',
		'type'=>'text',
		'attributes'=>['readonly'],
		'formatter'=>function($d,$row){
			if(isset($row['id'])):
				global $app;
				$db=$app->get_db();
				$file_data=mysqli_fetch_object($db->query("
					select archy_invoices.date, archy_firms.invoice_prefix 
					from archy_invoices 
					left join archy_firms on(archy_invoices.firm_id=archy_firms.id) 
					where archy_invoices.id=".$row["id"]
				));
				$year=(new DateTime($file_data->date))->format('Y');
				$prefix=$file_data->invoice_prefix;
				return $prefix.str_repeat("0",4-strlen($d)).$d.'-'.$year;
			else:
				return $d;
			endif;
		},
		'formatter_hide_in_form'=>true,
		'default_value'=>$app->next_document_number_by_year_and_firm('archy_invoices',$app->default_firm_id,date('Y'),'first_invoice_number',$ajax=false)
	),	
	'archy_invoices.customer_id'=>array(
		'label'=>'Cliente',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	'archy_invoices.invoice'=>array(
		'label'=>'Corpo fattura',
		'type'=>'text',
		'formatter'=>function($d,$row){return unserialize($d);},
		'hidden'=>''
	),
	'archy_invoices.payment_method_id'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'search',
		'intent'=>'archy_payment_methods',
		'lookup_text'=>'method'
	),
    'archy_invoices.bank_id'=>array(
		'label'=>'Banca',
		'type'=>'search',
		'intent'=>'archy_banks',
		'lookup_text'=>'archy_banks.nome',
        'default_value'=>$app->default_bank_id
	),
    'archy_invoices.payment_cond_type'=>array(
		'label'=>'Condizioni di pagamento',
		'type'=>'select', 
		'default_value'=>'TP02',
		'options_type'=>'array',
		'options'=>[
            ''=>'Non gestito',
            'TP01'=>'Pagamento a rate',
            'TP02'=>'Pagamento completo',
            'TP03'=>'Anticipo',
        ],
		'formatter'=>function($d,$row){
			$options=[
                ''=>'Non gestito',
                'TP01'=>'Pagamento a rate',
                'TP02'=>'Pagamento completo',
                'TP03'=>'Anticipo',
                ];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
	),


	'archy_invoices.due_date'=>array(
		'label'=>'Scadenza',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);}
	),
	'archy_invoices.stamp_duty'=>array(
		'label'=>'Imposta di bollo',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),
	'archy_invoices.taxable_amount'=>array(
		'label'=>'Totale imponibile',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_invoices.vat'=>array(
		'label'=>'Totale IVA',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_invoices.total'=>array(
		'label'=>'Totale Fattura',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_invoices.paid'=>array(
		'label'=>'Totale Riscosso',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_invoices.unpaid'=>array(
		'label'=>'Residuo da riscuotere',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_invoices.payment_status'=>array(
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
	'archy_invoices.notes'=>array(
		'label'=>'Note da riportare in fattura',
		'type'=>'editor',
		'formatter'=>function($d,$row){return strip_tags($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_invoices.sent_on'=>array(
		'label'=>'Data invio fattura',
		'type'=>'date',
		'formatter'=>function($d,$row){return $d!=NULL? App::latin_date_time($d) : 'Non inviata';},
		'hidden'=>true
	),
	'archy_invoices.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_invoices.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_invoices.id',
	'archy_firms.firm_name',
	'archy_firms.invoice_prefix',
	'archy_registry.label',
	'archy_invoices.number',
	'archy_invoices.date',
	'archy_invoices.total',
	'archy_invoices.paid',
	'archy_invoices.unpaid',
	'archy_invoices.payment_status'
);

$intent->uneditable_ids=array_column(mysqli_fetch_all($app->db->query("select id from archy_invoices where sdi_uuid!=''"),MYSQLI_ASSOC),'id');

$intent->extra_buttons=function($id,$row){
	global $app, $intent;

	$db=$app->get_db();
	$invoice=mysqli_fetch_object($db->query("select file_path,sent_on from archy_invoices where id=".$id));	
	$file=explode('/',$invoice->file_path);
	$file=end($file);

    /*
    modificare per tutte: glyphicon-envelope
    se già inviata cambiare colore/testo alla busta
    */
    $btn_class="btn btn-outline-warning";
    $title="Invia";
    if($invoice->sent_on) {
        $btn_class="btn-outline-success";
        $title="Reinvia";
    }

	$extra_button=' <a class="btn btn-outline-secondary" href="'.$invoice->file_path.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Fattura '.$file.'"><span class="far fa-file-archive"></span></a>';
	$extra_button.='
		<a class="btn '.$btn_class.'"
			href="/archy/app.php?do_action=send_invoice&invoice_id='.$id.'" 
			data-bs-toggle="tooltip" data-bs-placement="top" title="'.$title.' fattura"
			onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re la fattura?\')"
			><span class="far fa-envelope"></span></a>';
	$extra_button.=' <a class="btn btn-outline-info" href="/archy/app.php?do_action=duplicate_invoice&invoice_id='.$id.'" data-bs-toggle="tooltip" data-bs-placement="top" title="Duplica Fattura"><span class="fas fa-paste"></span></a>';
	
	if(!in_array($id,$app->intent->uneditable_ids)):
		$extra_button.='
			<a class="btn btn-danger" 
				href="/archy/app.php?do_action=send_to_sdi&invoice_id='.$id.'" 
				data-bs-toggle="tooltip" data-bs-placement="top" title="Invia al Sistema di Interscambio"
				onClick="return confirm(\'Inviare al Sistema di Interscambio?\')"
			><span class="fas fa-file-export"></span></a>';
	else:
		$extra_button.='
		<a class="btn btn-success" 
			href="/archy/app.php?do_action=download_sdi_xml&invoice_id='.$id.'" 
			data-bs-toggle="tooltip" data-bs-placement="top" title="XML Fattura elettronica"
		><span class="fas fa-file-import"></span></a>';
	endif;

	return $extra_button;
};

$intent->include_in_form='intents/includes/invoice_rows.php';
$intent->extra_action='store_invoice';
$intent->before_delete='&before_delete=invoice_before_delete';
$intent->after_delete='&after_delete=invoice_after_delete';
$intent->after_update='invoice_after_update';

$intent->delete_extra_notice='invoice_deletion';
//$intent->update_extra_notice='invoice_update';

$app->merge_intents($intent);
?>