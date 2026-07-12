<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_prestito_promessa';//nome tabella data-base
$intent->title='Promessa di Prestito';//titolo (plurale)
$intent->singular='Promessa di Prestito';//titolo (singolare)
$intent->add='Nuova Promessa di Prestito';//testo usato nei forms e nei pulsanti
$intent->view_title='Promessa di Prestito: archy_art_prestito_promessa.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_prestito_promessa.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_prestito_promessa.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_prestito_promessa.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_prestito_promessa.prestito_promessa_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
		'archy_art_prestito_promessa.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_prestito_promessa',
		'required'=>true,
		'grid_search_type'=>'search'
	),

		'archy_art_prestito_promessa.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
		'archy_art_prestito_promessa.data'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),

	/*	Sezione Archivio		*/
	
		'archy_art_prestito_promessa.nominativo_archivio'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.indirizzo_archivio'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.cap_archivio'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.comune_archivio'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.provincia_archivio'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
		'archy_art_prestito_promessa.stato_archivio'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.cod_fisc_archivio'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.p_iva_archivio'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.tipologia_documento_archivio'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.numero_documento_archivio'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_promessa.telefono_archivio'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->telefono_certificatore,
		'hidden'=>''
	),
                'archy_art_prestito_promessa.fax_archivio'=>array(
                        'label'=>'Fax',
                        'type'=>'text',
                        'default_value'=>@$defaults->fax_certificatore,
                        'hidden'=>''
                ),
		'archy_art_prestito_promessa.email_archivio'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),


		'archy_art_prestito_promessa.pec_archivio'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.luogo'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
	),

	'archy_art_prestito_promessa.nominativo_controparte'=>array(
		'label'=>'Nominativo controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.indirizzo_controparte'=>array(
		'label'=>'Indirizzo controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.cap_controparte'=>array(
		'label'=>'Cap controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.comune_controparte'=>array(
		'label'=>'Comune controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.provincia_controparte'=>array(
		'label'=>'Provincia controparte',
		'type'=>'text',
		'hidden'=>''
	),

    	'archy_art_prestito_promessa.stato_controparte'=>array(
		'label'=>'Nazione controparte',
		'type'=>'text',
		'hidden'=>''
	),
    

		'archy_art_prestito_promessa.cod_fisc_controparte'=>array(
		'label'=>'Codice Fiscale controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.p_iva_controparte'=>array(
		'label'=>'Partita Iva controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.tipologia_documento_controparte'=>array(
		'label'=>'Tipologia Documento di riconoscimento controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.numero_documento_controparte'=>array(
		'label'=>'Numero Documento di riconoscimento controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.telefono_controparte'=>array(
		'label'=>'Telefono controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.email_controparte'=>array(
		'label'=>'E-Mail controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.pec_controparte'=>array(
		'label'=>'Pec controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	
		'archy_art_prestito_promessa.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_prestito_promessa.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_prestito_promessa.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_promessa.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_promessa.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_prestito_promessa.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_promessa.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
		'archy_art_prestito_promessa.autorizzazione_ente'=>array(

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

	'archy_art_prestito_promessa.titolo_disposizione'=>array(
		'label'=>'Titolo disposizione',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_promessa.titolo_opera'=>array(
		'label'=>'Titolo dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.valore_opera'=>array(
		'label'=>'Valore dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.durata_cessione'=>array(
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
	
		'archy_art_prestito_promessa.diritti_utilizzazione'=>array(
		'label'=>'Diritti di utilizzazione',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritti_utilizzazione'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.dettagli_materiali'=>array(
		'label'=>'Dettagli materiali',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.dettagli_riproduzione'=>array(
		'label'=>'Dettagli riproduzione',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.termine_prestito'=>array(
		'label'=>'Termine prestito',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.data_promessa'=>array(
		'label'=>'Data promessa',
		'type'=>'date',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.scopo_utilizzo'=>array(
		'label'=>'Scopo di utilizzo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.evento_prestito'=>array(
		'label'=>'Evento prestito',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.sede_evento'=>array(
		'label'=>'Sede evento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.date_evento'=>array(
		'label'=>'Data evento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.data_inizio'=>array(
		'label'=>'Data inizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.data_fine'=>array(
		'label'=>'Data fine',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.progetto_evento'=>array(
		'label'=>'Descrizione progetto',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.tipo_di_cessione'=>array(
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
	
	'archy_art_prestito_promessa.termine_stipulazione'=>array(
		'label'=>'Termine stipulazione',
		'type'=>'date',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.periodo_disdetta'=>array(
		'label'=>'Periodo disdetta',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.importo_corrispettivo'=>array(
		'label'=>'Importo corrispettivo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.giorni_preavviso'=>array(
		'label'=>'Giorni di preavviso',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.titolo_originarieta'=>array(
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
	
	'archy_art_prestito_promessa.valore_assicurativo'=>array(
		'label'=>'Valore assicurativo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.nome_assicurazione'=>array(
		'label'=>'Nome assicurazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.sede_riconsegna'=>array(
		'label'=>'Sede di riconsegna',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_prestito_promessa.copyright_fotgrafico'=>array(
		'label'=>'Copyright fotografico',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.esposizione_esterno'=>array(
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
	
	'archy_art_prestito_promessa.individuazione_registrar'=>array(
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
	
	'archy_art_prestito_promessa.spese_registrar'=>array(
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
	
	'archy_art_prestito_promessa.concessione_diritti'=>array(
		'label'=>'Concessione dirittti',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('concessione_diritti'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('concessione_diritti',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	'archy_art_prestito_promessa.diritto_immagine'=>array(
		'label'=>'Diritti d\'immagine',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritto_immagine'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.nome_trasportatore'=>array(
		'label'=>'Nome trasportatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.tipo_sorveglianza'=>array(
		'label'=>'Tipo di sorveglianza',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.prestatatore_prestatario'=>array(
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
	
	'archy_art_prestito_promessa.tacito_rinnovo'=>array(
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
	
	'archy_art_prestito_promessa.opzione_registrar'=>array(
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
	
	
		'archy_art_prestito_promessa.nominativo_registrar'=>array(
		'label'=>'Nominativo Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_promessa.tel_registrar'=>array(
		'label'=>'Telefono Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.email_registrar'=>array(
		'label'=>'E-Mail Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_promessa.pec_registrar'=>array(
		'label'=>'Pec Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.dicitura_opera'=>array(
		'label'=>'Dicitura Opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.numero_copie'=>array(
		'label'=>'Numero di copie',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.sede_foro'=>array(
		'label'=>'Sede Foro competente',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_prestito_promessa.model_template'=>array(
		'label'=>' Modello Certificato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_prestito_promessa',
		'hidden'=>''
	),
	
	'archy_art_prestito_promessa.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_prestito_promessa.update_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
		
);

$intent->default_columns=array(
	'archy_art_prestito_promessa.id','archy_art_prestito_promessa.prestito_promessa_title','archy_art_prestito_promessa.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/prestito_promessa/prestito_promessa";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/prestito_promessa/".$id."_contratto_di_prestito_promessa.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_prestito_promessa.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);
