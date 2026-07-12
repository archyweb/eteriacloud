<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_prestito';//nome tabella data-base
$intent->title='Contratto di Prestito';//titolo (plurale)
$intent->singular='Contratto di Prestito';//titolo (singolare)
$intent->add='Nuovo Contratto di Prestito';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di Prestito: archy_art_prestito.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_prestito.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_prestito.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_prestito.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_prestito.prestito_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
		'archy_art_prestito.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_prestito',
		'required'=>true,
		'grid_search_type'=>'search'
	),

		'archy_art_prestito.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
		'archy_art_prestito.data'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),

	/*	Sezione Archivio		*/
	
		'archy_art_prestito.nominativo_archivio'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.indirizzo_archivio'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.cap_archivio'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.comune_archivio'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.provincia_archivio'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
	'archy_art_prestito.stato_archivio'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.cod_fisc_archivio'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.p_iva_archivio'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.tipologia_documento_archivio'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.numero_documento_archivio'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito.telefono_archivio'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->telefono_certificatore,
		'hidden'=>''
	),
	
        'archy_art_prestito.fax_archivio'=>array(
        'label'=>'Fax',
        'type'=>'text',
        'default_value'=>@$defaults->fax_certificatore,
        'hidden'=>''
        ),
		
		'archy_art_prestito.email_archivio'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),


		'archy_art_prestito.pec_archivio'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
	'archy_art_prestito.luogo'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
	),
	
	/* 	Sezione Controparte*/
	
	'archy_art_prestito.nominativo_controparte'=>array(
		'label'=>'Nominativo controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.indirizzo_controparte'=>array(
		'label'=>'Indirizzo controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.cap_controparte'=>array(
		'label'=>'Cap controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.comune_controparte'=>array(
		'label'=>'Comune controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.provincia_controparte'=>array(
		'label'=>'Provincia controparte',
		'type'=>'text',
		'hidden'=>''
	),

	'archy_art_prestito.stato_controparte'=>array(
		'label'=>'Nazione controparte',
		'type'=>'text',
		'hidden'=>''
	),
    
    
		'archy_art_prestito.cod_fisc_controparte'=>array(
		'label'=>'Codice Fiscale controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.p_iva_controparte'=>array(
		'label'=>'Partita Iva controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.tipologia_documento_controparte'=>array(
		'label'=>'Tipologia Documento di riconoscimento controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.numero_documento_controparte'=>array(
		'label'=>'Numero Documento di riconoscimento controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.telefono_controparte'=>array(
		'label'=>'Fax controparte',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.fax_controparte'=>array(
		'label'=>'Fax controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.email_controparte'=>array(
		'label'=>'E-Mail controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.pec_controparte'=>array(
		'label'=>'Pec controparte',
		'type'=>'text',
		'hidden'=>''
	),
	
	/*	fine controparte	*/

		/* 	Sezione RAPPRESENTANTE*/
		
		
		/*   Rappresentante   */
	
	
		'archy_art_prestito.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_prestito.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_prestito.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.fax_rappresentante'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_prestito.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
		'archy_art_prestito.autorizzazione_ente'=>array(

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
		
	
	/*    -------------------------------------------------------   */
	
	'archy_art_prestito.titolo_disposizione'=>array(
		'label'=>'Titolo disposizione',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.titolo_opera'=>array(
		'label'=>'Titolo dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.valore_opera'=>array(
		'label'=>'Valore dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.durata_cessione'=>array(
		'label'=>'Durata della cessione',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('durata_cessione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('durata_cessione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
		'archy_art_prestito.diritti_utilizzazione'=>array(
		'label'=>'Diritti di utilizzazione',
		'type'=>'multiple', 
		'options_type'=>'array',
		'options' => App::getArrayMultipleValues('diritti_utilizzazione'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_prestito.dettagli_materiali'=>array(
		'label'=>'Dettagli materiali',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito.dettagli_riproduzione'=>array(
		'label'=>'Dettagli riproduzione',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito.termine_prestito'=>array(
		'label'=>'Termine prestito',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito.data_promessa'=>array(
		'label'=>'Data promessa',
		'type'=>'date',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_prestito.scopo_utilizzo'=>array(
		'label'=>'Scopo di utilizzo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.evento_prestito'=>array(
		'label'=>'Evento prestito',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.sede_evento'=>array(
		'label'=>'Sede evento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.date_evento'=>array(
		'label'=>'Data evento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito.data_inizio'=>array(
		'label'=>'Data inizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito.data_fine'=>array(
		'label'=>'Data fine',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito.progetto_evento'=>array(
		'label'=>'Descrizione progetto',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.tipo_di_cessione'=>array(
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
	
	'archy_art_prestito.disponibilita_opera'=>array(
		'label'=>'Disponibilità dell\'Opera',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('disponibilita_opera'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('disponibilita_opera',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	'archy_art_prestito.termine_stipulazione'=>array(
		'label'=>'Termine stipulazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.periodo_disdetta'=>array(
		'label'=>'Periodo disdetta',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.giorni_preavviso'=>array(
		'label'=>'Giorni di preavviso',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.titolo_originarieta'=>array(
		'label'=>'Titolo originarietà',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('titolo_originarieta'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('titolo_originarieta',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_prestito.valore_assicurativo'=>array(
		'label'=>'Valore assicurativo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.nome_assicurazione'=>array(
		'label'=>'Nome assicurazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.sede_riconsegna'=>array(
		'label'=>'Sede di riconsegna',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.esposizione_esterno'=>array(
		'label'=>'Esposizione in esterno',	
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('esposizione_esterno'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('esposizione_esterno',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_prestito.individuazione_registrar'=>array(
		'label'=>'Individuazione registrar',	
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('individuazione_registrar'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('individuazione_registrar',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_prestito.spese_registrar'=>array(
		'label'=>'spese registrar',	
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('spese_registrar'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('spese_registrar',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_prestito.concessione_diritti'=>array(
		'label'=>'Concessione dirittti',	
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('concessione_diritti_senza_agg'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('concessione_diritti_senza_agg',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	'archy_art_prestito.diritto_immagine'=>array(
		'label'=>'Diritti d\'immagine',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritto_immagine'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_prestito.nome_trasportatore'=>array(
		'label'=>'Nome trasportatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.tipo_sorveglianza'=>array(
		'label'=>'Tipo di sorveglianza',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.prestatatore_prestatario'=>array(
		'label'=>'Prestatatore Prestatario',	
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('prestatatore_prestatario'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('prestatatore_prestatario',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_prestito.tacito_rinnovo'=>array(
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
	
	'archy_art_prestito.opzione_registrar'=>array(
		'label'=>'Opzione registrar',	
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('opzione_registrar'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('opzione_registrar',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
		'archy_art_prestito.nominativo_registrar'=>array(
		'label'=>'Nominativo Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito.tel_registrar'=>array(
		'label'=>'Telefono Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.email_registrar'=>array(
		'label'=>'E-Mail Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito.pec_registrar'=>array(
		'label'=>'Pec Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.dicitura_opera'=>array(
		'label'=>'Dicitura Opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.numero_copie'=>array(
		'label'=>'Numero di copie',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito.sede_foro'=>array(
		'label'=>'Sede Foro competente',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_prestito.model_template'=>array(
		'label'=>' Modello Certificato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_prestito',
		'hidden'=>''
	),
	
	'archy_art_prestito.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_prestito.update_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
	
	
	/*
	'archy_art_prestito.vivente'=>array(
		'label'=>'Artista vivente o Archivio',	
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>array(
			'0'=>'Artista Vivente',
			'1'=>'Archivio',
		),
		'formatter'=>function($d,$row){
			$values=array(
			'0'=>'di essere Autore dell’Opera di seguito individuata e meglio descritta, e ne attesta l’autenticità',
			'1'=>'che l’Opera di seguito individuata e meglio descritta è autentica',
			);
			return $values[$d];
		},
		'formatter_hide_in_form'=>'',
		'required'=>true,
		'hidden'=>''
	),*/

		
);

$intent->default_columns=array(
	'archy_art_prestito.id','archy_art_prestito.prestito_title','archy_art_prestito.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/prestito/prestito";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/prestito/".$id."_contratto_di_prestito.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_prestito.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);