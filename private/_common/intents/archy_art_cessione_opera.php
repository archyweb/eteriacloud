<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_cessione_opera';//nome tabella data-base
$intent->title='Contratto di Cessione di opera';//titolo (plurale)
$intent->singular='Contratto di Cessione di opera';//titolo (singolare)
$intent->add='Nuovo Contratto di Cessione di opera';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di Cessione di opera: archy_art_cessione_opera.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_cessione_opera.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_cessione_opera.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

	'archy_art_cessione_opera.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_opera.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_cessione_opera',
		'required'=>true,
		'grid_search_type'=>'search'
	),
        'archy_art_cessione_opera.cessione_opera_title'=>array(
		'label'=>'Titolo',
		'type'=>'text'
	),
		'archy_art_cessione_opera.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.titolo_opera',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
		'archy_art_cessione_opera.data'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	
		'archy_art_cessione_opera.luogo'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
	),

		'archy_art_cessione_opera.nominativo_cedente'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.indirizzo_cedente'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.cap_cedente'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.comune_cedente'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.provincia_cedente'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
		'archy_art_cessione_opera.stato_cedente'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.cod_fisc_cedente'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.p_iva_cedente'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.tipologia_documento_cedente'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.numero_documento_cedente'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.telefono_cedente'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->telefono_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.fax_cedente'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'default_value'=>@$defaults->fax_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.email_cedente'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_opera.pec_cedente'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
	'archy_art_cessione_opera.qualifica_cedente'=>array(
		'label'=>'Titolo disposizione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_cessione_opera.nominativo_acquirente'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.indirizzo_acquirente'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.cap_acquirente'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.comune_acquirente'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.provincia_acquirente'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_cessione_opera.stato_acquirente'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.cod_fisc_acquirente'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.p_iva_acquirente'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.tipologia_documento_acquirente'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.numero_documento_acquirente'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.telefono_acquirente'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.fax_acquirente'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.email_acquirente'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.pec_acquirente'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_cessione_opera.qualifica_acquirente'=>array(
		'label'=>'Titolo di proprietà dell\'acquirente',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.model_template'=>array(
		'label'=>' Modello Certificato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_cessione_opera',
		'hidden'=>''
	),
	
	/*  rappresentante */
	
	/*   Rappresentante   */
	
	
		'archy_art_cessione_opera.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_cessione_opera.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_cessione_opera.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_cessione_opera.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_cessione_opera.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_cessione_opera.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_opera.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_cessione_opera.autorizzazione_ente'=>array(
		'label'=>'Autorizzazione ente pubblico',
		'type'=>'bulk_upload',
		'formatter'=>function($d,$row){

			if($d!=''):

				$docs=unserialize($d);

				$return='<ul>';

				foreach($docs as $path):

					$tmp=explode('/',$path);

					$return.='<li><a href="'.$path.'" download>'.end($tmp).'</a></li>';

				endforeach;

				$return.='</ul>';

				return $return;

			endif;

		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
	),
        'archy_art_cessione_opera.titolo_opera'=>array(
		'label'=>'Nome dell\'Opera',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.valore_opera'=>array(
		'label'=>'Valore dell\'Opera',
		'type'=>'text',
		'hidden'=>''
	),		
	'archy_art_cessione_opera.rappresentante_cedente'=>array(
		'label'=>'Legale Rappresentante cedente',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.vivente'=>array(
		'label'=>'Artista vivente o Archivio',	
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('vivente'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('vivente',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.titolo_originarieta'=>array(
		'label'=>'Titolarità',	
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('titolo_originarieta'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('titolo_originarieta',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.referente_restauro'=>array(
		'label'=>'Referente del Restauro',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.nome_archivio_artista'=>array(
		'label'=>'Nome Archivio',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.dicitura_opera'=>array(
		'label'=>'Dicitura opera',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_cessione_opera.indirizzo_fatturazione'=>array(
		'label'=>'Indirizzo fatturazione',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_cessione_opera.modalita_pagamento'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.spese_a_carico'=>array(
		'label'=>'Spese a carico di:',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('spese_a_carico'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('spese_a_carico',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_art_cessione_opera.tipo_di_cessione'=>array(
		'label'=>'Titolo cessione',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('tipo_di_cessione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tipo_di_cessione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.durata_cessione'=>array(
		'label'=>'Durata cessione',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('durata_cessione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('durata_cessione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_art_cessione_opera.facolta_di_prestito'=>array(
		'label'=>'riconosce / non riconosce',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('facolta_di_prestito'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('facolta_di_prestito',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.diritti_di_utilizzazione'=>array(
		'label'=>'Diritti di utilizzazione economica',	
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('diritti_di_utilizzazione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('diritti_di_utilizzazione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_art_cessione_opera.dettagli_riproduzione'=>array(
		'label'=>'Dettagli di riproduzione',
		'type'=>'editor',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.copyright'=>array(
		'label'=>'Copyright',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.numero_copie_fornite'=>array(
		'label'=>'Numero di copie fornite',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.valore_assicurativo'=>array(
		'label'=>'Valore Assicurato',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.nome_assicurazione'=>array(
		'label'=>'Nominato Assicurazione',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.nome_trasportatore'=>array(
		'label'=>'Nominativo Trasportatore',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.sede_foro'=>array(
		'label'=>'Sede Foro competente',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_cessione_opera.corrispettivo'=>array(
		'label'=>'Cessione con o senza corrispettivo',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('corrispettivo'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('corrispettivo',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_opera.importo_corrispettivo'=>array(
		'label'=>'Importo corrispettivo IVA inclusa',
		'type'=>'text',
		//'required'=>true,
		'hidden'=>''
	),	
	'archy_art_cessione_opera.indirizzo_fattura'=>array(
		'label'=>'Indirizzo Fatturazione',
		'type'=>'text',
		//'required'=>true,
		'hidden'=>''
	),
	'archy_art_cessione_opera.modalita_pagamento_corrispettivo'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'text',
		//'required'=>true,
		'hidden'=>''
	),
	'archy_art_cessione_opera.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_cessione_opera.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_cessione_opera.id','archy_art_cessione_opera.cessione_opera_title','archy_art_cessione_opera.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/cessione_opera/cessione_opera";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/cessione_opera/".$id."_cessione_opera.pdf";
                if (file_exists($physical_path.$file_path)) {
                       $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_cessione_opera.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);

?>

