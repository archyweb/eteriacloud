<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_prestito_definitivo';//nome tabella data-base
$intent->title='Contratto di Prestito Definitivo';//titolo (plurale)
$intent->singular='Contratto di Prestito Definitivo';//titolo (singolare)
$intent->add='Nuovo Contratto di Prestito Definitivo';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di Prestito Definitivo: archy_art_prestito_definitivo.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_prestito_definitivo.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_prestito_definitivo.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_prestito_definitivo.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_prestito_definitivo.prestito_definitivo_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
		'archy_art_prestito_definitivo.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_prestito_definitivo',
		'required'=>true,
		'grid_search_type'=>'search'
	),

		'archy_art_prestito_definitivo.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
		'archy_art_prestito_definitivo.data'=>array(
		'label'=>'Data del contratto',
		'type'=>'date',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.nominativo_archivio'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.indirizzo_archivio'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.cap_archivio'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.comune_archivio'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.provincia_archivio'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
	
	'archy_art_prestito_definitivo.stato_archivio'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.cod_fisc_archivio'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.p_iva_archivio'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.tipologia_documento_archivio'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.numero_documento_archivio'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.telefono_archivio'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->telefono_certificatore,
		'hidden'=>''
	),
            'archy_art_prestito_definitivo.fax_archivio'=>array(
                        'label'=>'Fax',
                        'type'=>'text',
                        'default_value'=>@$defaults->fax_certificatore,
                        'hidden'=>''
                ),
		'archy_art_prestito_definitivo.email_archivio'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),


		'archy_art_prestito_definitivo.pec_archivio'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.luogo'=>array(
		'label'=>'Luogo di stipula',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
		'hidden'=>''
	),

	'archy_art_prestito_definitivo.nominativo_controparte'=>array(
		'label'=>'Nominativo controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.indirizzo_controparte'=>array(
		'label'=>'Indirizzo controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.cap_controparte'=>array(
		'label'=>'Cap controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.comune_controparte'=>array(
		'label'=>'Comune controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.provincia_controparte'=>array(
		'label'=>'Provincia controparte',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.stato_controparte'=>array(
		'label'=>'Nazione controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.cod_fisc_controparte'=>array(
		'label'=>'Codice Fiscale controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.p_iva_controparte'=>array(
		'label'=>'Partita Iva controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.tipologia_documento_controparte'=>array(
		'label'=>'Tipologia Documento di riconoscimento controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.numero_documento_controparte'=>array(
		'label'=>'Numero Documento di riconoscimento controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.telefono_controparte'=>array(
		'label'=>'Telefono controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.email_controparte'=>array(
		'label'=>'E-Mail controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.pec_controparte'=>array(
		'label'=>'Pec controparte',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_definitivo.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_prestito_definitivo.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_definitivo.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_definitivo.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_prestito_definitivo.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_definitivo.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
		'archy_art_prestito_definitivo.autorizzazione_ente'=>array(

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

	'archy_art_prestito_definitivo.titolo_disposizione'=>array(
		'label'=>'Titolo disposizione',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_definitivo.titolo_opera'=>array(
		'label'=>'Titolo dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.valore_opera'=>array(
		'label'=>'Valore dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.durata_cessione'=>array(
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
	
		'archy_art_prestito_definitivo.diritti_utilizzazione'=>array(
		'label'=>'Diritti di utilizzazione',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritti_utilizzazione'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.dettagli_materiali'=>array(
		'label'=>'Dettagli materiali',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.dettagli_riproduzione'=>array(
		'label'=>'Dettagli riproduzione',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.termine_prestito'=>array(
		'label'=>'Termine prestito',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.data_promessa'=>array(
		'label'=>'Data promessa',
		'type'=>'date',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.scopo_utilizzo'=>array(
		'label'=>'Scopo di utilizzo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.evento_prestito'=>array(
		'label'=>'Evento prestito',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.sede_evento'=>array(
		'label'=>'Sede evento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.date_evento'=>array(
		'label'=>'Data evento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.data_inizio'=>array(
		'label'=>'Data inizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.data_fine'=>array(
		'label'=>'Data fine',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.progetto_evento'=>array(
		'label'=>'Descrizione progetto',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.tipo_di_cessione'=>array(
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
	
	'archy_art_prestito_definitivo.termine_stipulazione'=>array(
		'label'=>'Termine stipulazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.periodo_disdetta'=>array(
		'label'=>'Periodo disdetta',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.giorni_preavviso'=>array(
		'label'=>'Giorni di preavviso',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.titolo_originarieta'=>array(
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
	
	'archy_art_prestito_definitivo.valore_assicurativo'=>array(
		'label'=>'Valore assicurativo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.nome_assicurazione'=>array(
		'label'=>'Nome assicurazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.sede_riconsegna'=>array(
		'label'=>'Sede di riconsegna',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.esposizione_esterno'=>array(
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
	
	'archy_art_prestito_definitivo.individuazione_registrar'=>array(
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
	
	'archy_art_prestito_definitivo.spese_registrar'=>array(
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
	
	'archy_art_prestito_definitivo.concessione_diritti'=>array(
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
	
	
	'archy_art_prestito_definitivo.diritto_immagine'=>array(
		'label'=>'Diritti d\'immagine',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritto_immagine'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.nome_trasportatore'=>array(
		'label'=>'Nome trasportatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.tipo_sorveglianza'=>array(
		'label'=>'Tipo di sorveglianza',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.prestatatore_prestatario'=>array(
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
	
	'archy_art_prestito_definitivo.tacito_rinnovo'=>array(
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
	
	'archy_art_prestito_definitivo.opzione_registrar'=>array(
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
		
		'archy_art_prestito_definitivo.nominativo_registrar'=>array(
		'label'=>'Nominativo Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_prestito_definitivo.tel_registrar'=>array(
		'label'=>'Telefono Registrar',
		'type'=>'text',
		'hidden'=>''
	),
		'archy_art_prestito_definitivo.email_registrar'=>array(
		'label'=>'E-Mail Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.pec_registrar'=>array(
		'label'=>'Pec Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.dicitura_opera'=>array(
		'label'=>'Dicitura Opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.numero_copie'=>array(
		'label'=>'Numero di copie',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.sede_foro'=>array(
		'label'=>'Sede Foro competente',
		'type'=>'text',
		'hidden'=>''
	),	
	
	'archy_art_prestito_definitivo.model_template'=>array(
		'label'=>' Modello Certificato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_prestito_definitivo',
		'hidden'=>''
	),
	
	'archy_art_prestito_definitivo.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_prestito_definitivo.update_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
		
);

$intent->default_columns=array(
	'archy_art_prestito_definitivo.id','archy_art_prestito_definitivo.prestito_definitivo_title','archy_art_prestito_definitivo.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/prestito_definitivo/prestito_definitivo";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/prestito_definitivo/".$id."_contratto_di_prestito_definitivo.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_prestito_definitivo.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);