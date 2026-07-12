<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_cessione_immagine';//nome tabella data-base
$intent->title='Contratto di cessione diritti d\'immagine';//titolo (plurale)
$intent->singular='Contratto di cessione diritti d\'immagine';//titolo (singolare)
$intent->add='Nuovo Contratto di cessione diritti d\'immagine';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di cessione diritti d\'immagine: archy_art_cessione_immagine.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_cessione_immagine.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_cessione_immagine.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

        'archy_art_cessione_immagine.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
         'archy_art_cessione_immagine.cessione_immagine_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
	'archy_art_cessione_immagine.model_id'=>array(
		'label'=>'Modello contratto',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_cessione_immagine',
		'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),
	
	'archy_art_cessione_immagine.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
	'archy_art_cessione_immagine.titolo_opera'=>array(
		'label'=>'Nome Opera',
		'type'=>'text',
		'hidden'=>''
	),	
	
        'archy_art_cessione_immagine.data'=>array(
		'label'=>'Data contratto',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	
	'archy_art_cessione_immagine.luogo'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
		'required'=>true,
		'hidden'=>''
	),
	
		'archy_art_cessione_immagine.nominativo_ente'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_cessione_immagine.indirizzo_ente'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_cessione_immagine.cap_ente'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_cessione_immagine.comune_ente'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'required'=>true,
		'hidden'=>''
	),

		'archy_art_cessione_immagine.provincia_ente'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'required'=>true,
		'hidden'=>''
	),
	

		'archy_art_cessione_immagine.cod_fisc_ente'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_cessione_immagine.p_iva_ente'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

	
	'archy_art_cessione_immagine.tipologia_documento_ente'=>array(
		'label'=>'Tipologia Documento',
		'default_value'=>@$defaults->telefono_certificatore,
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_cessione_immagine.numero_documento_ente'=>array(
		'label'=>'Numero Documento',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_cessione_immagine.telefono_ente'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),
	
	'archy_art_cessione_immagine.fax_ente'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'default_value'=>@$defaults->fax_certificatore,
		'hidden'=>''
	),


		'archy_art_cessione_immagine.email_ente'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),
	
	'archy_art_cessione_immagine.pec_ente'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),

	
	/*  Controparte */
	
		'archy_art_cessione_immagine.nominativo_controparte'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.indirizzo_controparte'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.cap_controparte'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.comune_controparte'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.provincia_controparte'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_cessione_immagine.cod_fisc_controparte'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.p_iva_controparte'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.tipologia_documento_controparte'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.numero_documento_controparte'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),	
		'archy_art_cessione_immagine.telefono_controparte'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.fax_controparte'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_immagine.email_controparte'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.pec_controparte'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_cessione_immagine.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_immagine.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_immagine.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_immagine.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),	

		'archy_art_cessione_immagine.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_immagine.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),	
		'archy_art_cessione_immagine.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.fax_rappresentante'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_cessione_immagine.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_cessione_immagine.tipologia_evento'=>array(
		'label'=>'Descrizione tipologia evento',
		'type'=>'editor',
		//'editor_height'=>'400',
		//'editor_purpose'=>'cessione_immagine',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.esclusiva'=>array(
		'label'=>'Contratto in esclusiva o non esclusiva',
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'esclusiva',
			'1'=>'non esclusiva',
		),
		'formatter'=>function($d,$row){
			$values=array(
			'0'=>'esclusiva',
			'1'=>'non esclusiva',
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.durata_tempo'=>array(
		'label'=>'Indicare durata in giorni',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.durata_cessione'=>array(
		'label'=>'Durata cessione',
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'temporanea di giorni [durata_giorni]',
			'1'=>'permanente',
		),
		'formatter'=>function($d,$row){
			$values=array(
			'0'=>'temporanea di giorni [durata_giorni]',
			'1'=>'permanente',
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.data_inizio'=>array(
		'label'=>'Data inizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.data_fine'=>array(
		'label'=>'Data fine',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.tacito_rinnovo'=>array(
		'label'=>'Tacito rinnovo',	
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'É previsto',
			'1'=>'É escluso',
		),
		'formatter'=>function($d,$row){
			$values=array(
			'0'=>'É previsto',
			'1'=>'É escluso',
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	'archy_art_cessione_immagine.tipologia_cessione'=>array(
		'label'=>'Cessione con o senza corrispettivo',
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'Il Cessionario si obbliga a versare al Cedente, quale corrispettivo per la cessione del Diritto di riproduzione, l\'importo iva inclusa di €...',
			'1'=>'Per la cessione del Diritto di riproduzione, non è incluso alcun un corrispettivo',
		),
		'formatter'=>function($d,$row){
			$values=array(
			'0'=>'Il Cessionario si obbliga a versare al Cedente, quale corrispettivo per la cessione del Diritto di riproduzione, l\'importo iva inclusa di €...',
			'1'=>'Per la cessione del Diritto di riproduzione, non è incluso alcun un corrispettivo',
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_art_cessione_immagine.importo_corrispettivo'=>array(
		'label'=>'Importo corrispettivo Iva inclusa',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.indirizzo_fattura'=>array(
		'label'=>'Indirizzo Fatturazione',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'archy_art_cessione_immagine.modalita_pagamento'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),	
	'archy_art_cessione_immagine.sede_foro'=>array(
		'label'=>'Foro Competente',
		'type'=>'text',
		'default_value'=>@$defaults->foro_competente,
		'hidden'=>''
	),	
        'archy_art_cessione_immagine.foro'=>array(
		'label'=>'Foro Competente',
		'type'=>'text',
		'default_value'=>@$defaults->foro_competente,
		'hidden'=>''
	),
	'archy_art_cessione_immagine.periodo_disdetta'=>array(
		'label'=>'Periodo disdetta',
		'type'=>'text',
		'hidden'=>''
	),	
        'archy_art_cessione_immagine.originario_derivato'=>array(
		'label'=>'Originario o derivato',	
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'originario',
			'1'=>'derivato',
		),
		'formatter'=>function($d,$row){
			$values=array(
			'0'=>'originario',
			'1'=>'derivato',
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),	
'archy_art_cessione_immagine.dicitura_opera'=>array(
		'label'=>'Dicitura opera',
		'type'=>'text',
		'hidden'=>'',
		//'required'=>true
	),
	
		'archy_art_cessione_immagine.model_template'=>array(
		'label'=>' Modello Contratto di cessione diritti d\'immagine',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		//'editor_purpose'=>'cessione_immagine',
                'placeholder'=>true,
                'placeholder_origin' => 'archy_art_cessione_immagine',
		'hidden'=>''
	),

		'archy_art_cessione_immagine.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_cessione_immagine.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_cessione_immagine.id','archy_art_cessione_immagine.cessione_immagine_title','archy_art_cessione_immagine.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/cessione_immagine/cessione_immagine";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/cessione_immagine/".$id."cessione_immagine.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_cessione_immagine.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);
