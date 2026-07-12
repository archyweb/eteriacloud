<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_diritto_riproduzione_immagine';//nome tabella data-base
$intent->title='Contratto cessione diritti patrimoniali';//titolo (plurale)
$intent->singular='Contratto cessione diritti patrimoniali';//titolo (singolare)
$intent->add='Nuovo Contratto cessione diritti patrimoniali';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto cessione diritti patrimoniali: archy_art_diritto_riproduzione_immagine.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_diritto_riproduzione_immagine.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_diritto_riproduzione_immagine.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_diritto_riproduzione_immagine.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
         'archy_art_diritto_riproduzione_immagine.diritto_riproduzione_immagine_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
	'archy_art_diritto_riproduzione_immagine.model_id'=>array(
		'label'=>'Modello contratto',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_diritto_riproduzione_immagine',
		'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
	'archy_art_diritto_riproduzione_immagine.titolo_opera'=>array(
		'label'=>'Nome Opera',
		'type'=>'text',
		'hidden'=>''
	),	
	
		'archy_art_diritto_riproduzione_immagine.data'=>array(
		'label'=>'Data contratto',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		//'hidden'=>''
	),
	
	
	'archy_art_diritto_riproduzione_immagine.luogo'=>array(
		'label'=>'Luogo contratto',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
		'required'=>true,
		//'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.nominativo_cedente'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.indirizzo_cedente'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.cap_cedente'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.comune_cedente'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.provincia_cedente'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.stato_cedente'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'required'=>true,
		'hidden'=>''
	),
	

		'archy_art_diritto_riproduzione_immagine.cod_fisc_cedente'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.p_iva_cedente'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

	
	'archy_art_diritto_riproduzione_immagine.tipologia_documento_cedente'=>array(
		'label'=>'Tipologia Documento',
		'default_value'=>@$defaults->telefono_certificatore,
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.numero_documento_cedente'=>array(
		'label'=>'Numero Documento',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.telefono_cedente'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.fax_cedente'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'default_value'=>@$defaults->fax_certificatore,
		'hidden'=>''
	),


		'archy_art_diritto_riproduzione_immagine.email_cedente'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.pec_cedente'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.nominativo_cessionario'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.indirizzo_cessionario'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.cap_cessionario'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.comune_cessionario'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.provincia_cessionario'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.stato_cessionario'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.cod_fisc_cessionario'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.p_iva_cessionario'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
	'archy_art_diritto_riproduzione_immagine.tipologia_documento_cessionario'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.numero_documento_cessionario'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.telefono_cessionario'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.fax_cessionario'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_diritto_riproduzione_immagine.email_cessionario'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.pec_cessionario'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),


	
		'archy_art_diritto_riproduzione_immagine.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_diritto_riproduzione_immagine.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_diritto_riproduzione_immagine.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_diritto_riproduzione_immagine.tipologia_evento'=>array(
		'label'=>'Descrizione tipologia evento',
		'type'=>'editor',
		'hidden'=>''
	),
	
	
	'archy_art_diritto_riproduzione_immagine.autorizzazione_ente'=>array(

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

		//'hidden'=>''

	),
	
		'archy_art_diritto_riproduzione_immagine.esclusiva'=>array(
		'label'=>'Contratto in esclusiva o non esclusiva',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('esclusiva'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('esclusiva',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	
	'archy_art_diritto_riproduzione_immagine.titolo_proprieta_cedente'=>array(
		'label'=>'Titolo proprietà',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('titolo_proprieta_cedente'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('titolo_proprieta_cedente',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	
	
	
		'archy_art_diritto_riproduzione_immagine.durata_tempo'=>array(
		'label'=>'Indicare durata in giorni',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.diritti_patrimoniali'=>array(
		'label'=>'Indicare diritti patrimoniali',
		'type'=>'text',
		'hidden'=>''
	),
	
	
		'archy_art_diritto_riproduzione_immagine.durata_cessione'=>array(
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
	
		'archy_art_diritto_riproduzione_immagine.data_inizio'=>array(
		'label'=>'Data inizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.data_fine'=>array(
		'label'=>'Data fine',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
		
		'archy_art_diritto_riproduzione_immagine.tacito_rinnovo'=>array(
		'label'=>'Tacito rinnovo',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('tacito_rinnovo'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tacito_rinnovo',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.originario_derivato'=>array(
		'label'=>'Titolo originarietà',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('originario_derivato'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('originario_derivato',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
		/*'archy_art_diritto_riproduzione_immagine.modalita_riproduzione'=>array(
		'label'=>'Modalità di riproduzione',
		'type'=>'text',
		'hidden'=>''
	),*/
	
		'archy_art_diritto_riproduzione_immagine.tipologia_cessione'=>array(
		'label'=>'Cessione con o senza corrispettivo',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('tipologia_cessione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tipologia_cessione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
		'archy_art_diritto_riproduzione_immagine.importo_corrispettivo'=>array(
		'label'=>'Importo corrispettivo Iva Inclusa',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.indirizzo_fattura'=>array(
		'label'=>'Indirizzo Fatturazione',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.modalita_pagamento'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_diritto_riproduzione_immagine.sede_foro'=>array(
		'label'=>'Foro Competente',
		'type'=>'text',
		'default_value'=>@$defaults->foro_competente,
		'hidden'=>''
	),
	
	
		'archy_art_diritto_riproduzione_immagine.dicitura_opera'=>array(
		'label'=>'Dicitura opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_diritto_riproduzione_immagine.tipo_di_cessione'=>array(
		'label'=>'Tipologia di Cessione',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('tipo_di_cessione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tipo_di_cessione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	/*'archy_art_diritto_riproduzione_immagine.titolo_originarieta'=>array(
		'label'=>'Titolo Originarietà',
		'type'=>'text',
		'hidden'=>''
	),*/
	
	'archy_art_diritto_riproduzione_immagine.periodo_disdetta'=>array(
		'label'=>'Periodo disdetta',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	
		'archy_art_diritto_riproduzione_immagine.model_template'=>array(
		'label'=>' Modello Contratto diritti di riproduzione immagine',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_diritto_riproduzione_immagine',
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_diritto_riproduzione_immagine.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_diritto_riproduzione_immagine.id','archy_art_diritto_riproduzione_immagine.diritto_riproduzione_immagine_title','archy_art_diritto_riproduzione_immagine.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/cessione_diritti_patrimoniali/cessione_diritti_patrimoniali";        
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/cessione_diritti_patrimoniali/".$id."_cessione_diritti_patrimoniali.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_diritti_riproduzione.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);