<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_noleggio';//nome tabella data-base
$intent->title='Contratto di noleggio';//titolo (plurale)
$intent->singular='Contratto di noleggio';//titolo (singolare)
$intent->add='Nuovo Contratto di noleggio';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di noleggio: archy_art_noleggio.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_noleggio.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_noleggio.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_noleggio.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
        'archy_art_noleggio.noleggio_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
		'archy_art_noleggio.model_id'=>array(
		'label'=>'Modello Contratto',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_noleggio',
		'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),

		'archy_art_noleggio.opera_id'=>array(
		'label'=>'Seleziona Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		//'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),
	
		'archy_art_noleggio.data'=>array(
		'label'=>'Data',
		'type'=>'date',
		'hidden'=>'',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),

		'archy_art_noleggio.luogo_contratto'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.titolo_opera'=>array(
		'label'=>'Nome dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.valore_opera'=>array(
		'label'=>'Valore dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	

	'archy_art_noleggio.archivio_artista'=>array(
		'label'=>'Archivio o Artista',	
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('archivio_artista'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('archivio_artista',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.archivio_autore'=>array(
		'label'=>'Archivio o Autore',	
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('archivio_autore'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('archivio_autore',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.titolo_disposizione'=>array(
		'label'=>'Titolo disposizione',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_noleggio.nominativo_archivio'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.indirizzo_archivio'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.cap_archivio'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.comune_archivio'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.provincia_archivio'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
		'archy_art_noleggio.stato_archivio'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.cod_fisc_archivio'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.p_iva_archivio'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.tipologia_documento_archivio'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.numero_documento_archivio'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.telefono_archivio'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->telefono_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.fax_archivio'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'default_value'=>@$defaults->fax_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.email_archivio'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),

		'archy_art_noleggio.pec_archivio'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
	'archy_art_noleggio.nominativo_controparte'=>array(
		'label'=>'Nominativo Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.indirizzo_controparte'=>array(
		'label'=>'Indirizzo Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.cap_controparte'=>array(
		'label'=>'Cap Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.comune_controparte'=>array(
		'label'=>'Comune Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.provincia_controparte'=>array(
		'label'=>'Provincia Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_noleggio.stato_controparte'=>array(
		'label'=>'Nazione Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.cod_fisc_controparte'=>array(
		'label'=>'Codice Fiscale Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.p_iva_controparte'=>array(
		'label'=>'Partita Iva Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.tipologia_documento_controparte'=>array(
		'label'=>'Tipologia Documento di riconoscimento Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.numero_documento_controparte'=>array(
		'label'=>'Numero Documento di riconoscimento Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.telefono_controparte'=>array(
		'label'=>'Telefono Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.fax_controparte'=>array(
		'label'=>'Fax Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.email_controparte'=>array(
		'label'=>'E-Mail Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.pec_controparte'=>array(
		'label'=>'Pec Utilizzatore',
		'type'=>'text',
		'hidden'=>''
	),


	
	/*   Rappresentante   */
	
	
		'archy_art_noleggio.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_noleggio.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
		'archy_art_noleggio.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_noleggio.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_noleggio.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_noleggio.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_noleggio.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
		'archy_art_noleggio.autorizzazione_ente'=>array(

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
	
	'archy_art_noleggio.scopo_utilizzo'=>array(
		'label'=>'Scopo di utilizzo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.evento_noleggio'=>array(
		'label'=>'Evento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.date_evento'=>array(
		'label'=>'Data evento',
		'type'=>'date',
                'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_noleggio.sede_evento'=>array(
		'label'=>'Sede dell\'evento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.progetto_evento'=>array(
		'label'=>'Tipo di progetto',
		'type'=>'multiple', 
		'options_type'=>'array',
		'options' => App::getArrayMultipleValues('progetto_evento'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.data_inizio'=>array(
		'label'=>'Data inizio noleggio',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_noleggio.data_fine'=>array(
		'label'=>'Data fine noleggio',
		'type'=>'date',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_noleggio.giorni_preavviso'=>array(
		'label'=>'Giorni di preavviso',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.valore_assicurativo'=>array(
		'label'=>'Valore assicurativo',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.nome_assicurazione'=>array(
		'label'=>'Nominativo assicurazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.sede_riconsegna'=>array(
		'label'=>'Indirizzo sede per la riconsegna',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.tempo_concessione'=>array(
		'label'=>'Tempo di concessione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.nome_trasportatore'=>array(
		'label'=>'Nome del trasportare',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.dettagli_materiali'=>array(
		'label'=>'Dettagli materiali',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('dettagli_materiali'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
              
	'archy_art_noleggio.tipo_sorveglianza'=>array(
		'label'=>'Tipologia di sorveglianza',
		'type'=>'select', 
		'default_value'=>'0',
		'options_type'=>'array',
                'options' => App::getArrayValues('tipo_sorveglianza'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tipo_sorveglianza',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.descrizione_sorveglianza'=>array(
		'label'=>'Descrizione tipo di sorveglianza',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'hidden'=>'',
		//'required'=>true,
	),

	'archy_art_noleggio.esposizione_esterna'=>array(
		'label'=>'Concessione esposizione in esterna',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('esposizione_esterna'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('esposizione_esterna',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.opzione_registrar'=>array(
		'label'=>'Opzione Registrar',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('opzione_registrar'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('opzione_registrar',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.periodicita_canone'=>array(
		'label'=>'Periodicità del canone',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('periodicita_canone'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('periodicita_canone',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.corrispettivo_noleggio'=>array(
		'label'=>'Corrispettivo per il noleggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.esposizione_iva'=>array(
		'label'=>'Esposizione Iva',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('esposizione_iva'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('esposizione_iva',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.valore_iva'=>array(
		'label'=>'Importo Iva',
		'type'=>'text',
		'hidden'=>''
	),
	
	/*'archy_art_noleggio.titolo_originarieta'=>array(
		'label'=>'Titolo originarietà',
		'type'=>'text',
		'hidden'=>''
	),*/
	
	
	'archy_art_noleggio.giorno_pagamento'=>array(
		'label'=>'Giorno nel mese per pagamento',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('giorno_pagamento'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('giorno_pagamento',$d);
                },		
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	
	'archy_art_noleggio.concessione_diritti'=>array(
		'label'=>'Concessione diritti d\'immagine',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('concessione_diritti_noleggio'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('concessione_diritti_noleggio',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),

	'archy_art_noleggio.diritto_immagine'=>array(
		'label'=>'Diritti d\'immagine',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritto_immagine'),
		//'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_noleggio.diritti_immagine_corrispettivo'=>array(
		'label'=>'Corrispettivo per concessione diritti d\'immagine',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_noleggio.modalita_pagamento'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.periodicita_pagamento'=>array(
		'label'=>'Periodicità di pagamento',
		'type'=>'select', 
		'options_type'=>'array',
		'options' => App::getArrayValues('periodicita_pagamento'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('periodicita_pagamento',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_noleggio.provvigione_vendita'=>array(
		'label'=>'Importo provvigione in caso di vendita',
		'type'=>'select', 
		'default_value'=>'0',
		'options_type'=>'array',
		'options' => App::getArrayValues('provvigione_vendita'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('provvigione_vendita',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	), 
	
	'archy_art_noleggio.provvigione_percentuale'=>array(
		'label'=>'Percentuale provvigione in caso di vendita',
		'type'=>'text',
		'hidden'=>'',
                'help_block'=>'(indicare valore importo senza simbolo %)',
		'required'=>true
	),
	
	'archy_art_noleggio.dettagli_riproduzione'=>array(
		'label'=>'Dettagli riproduzione',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_noleggio.numero_copie'=>array(
		'label'=>'numero copie',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.se_presenti_piu_opere'=>array(
		'label'=>'se presenti più opere',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.integrazioni'=>array(
		'label'=>'Integrazioni e accordi particolari',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_noleggio.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.sede_foro'=>array(
		'label'=>'Sede Foro',
		'type'=>'text',
		'hidden'=>''
	),

	/* nuovi campi 27-01-2020*/
	
	'archy_art_noleggio.tacito_rinnovo'=>array(
		'label'=>'Tacito rinnovo',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('tacito_rinnovo'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tacito_rinnovo',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),

'archy_art_noleggio.periodo_disdetta'=>array(
		'label'=>'Periodo disdetta',
		'type'=>'text',
		'hidden'=>''
	),
	
'archy_art_noleggio.esclusiva_non_esclusiva'=>array(
		'label'=>'Esclusività contrattuale',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('esclusiva_non_esclusiva'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('esclusiva_non_esclusiva',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
'archy_art_noleggio.originario_derivato'=>array(
		'label'=>'Originario o derivato',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('originario_derivato'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('originario_derivato',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
'archy_art_noleggio.utilizzatore_archivio'=>array(
		'label'=>'Utilizzatore o Archivio',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('utilizzatore_archivio'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('utilizzatore_archivio',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
'archy_art_noleggio.individuazione_registrar'=>array(
		'label'=>'Individuazione registrar',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('individuazione_registrar'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('individuazione_registrar',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
		'archy_art_noleggio.nominativo_registrar'=>array(
		'label'=>'Nominativo Registrar',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_noleggio.tel_registrar'=>array(
		'label'=>'Telefono Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.email_registrar'=>array(
		'label'=>'E-Mail Registrar',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_noleggio.pec_registrar'=>array(
		'label'=>'Pec Registrar',
		'type'=>'text',
		'hidden'=>''
	),

	

'archy_art_noleggio.spese_registrar'=>array(
		'label'=>'Spese registrar',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('spese_registrar'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('spese_registrar',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),	
	
	
'archy_art_noleggio.sconto_canone_per_acquisto'=>array(
		'label'=>'Deduzione canone da acquisto',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('sconto_canone_per_acquisto'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('sconto_canone_per_acquisto',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),	
	
	
		'archy_art_noleggio.dicitura_opera'=>array(
		'label'=>'Dicitura opera',
		'type'=>'text',
		'hidden'=>'',
		//'required'=>true
	),

		'archy_art_noleggio.model_template'=>array(
		'label'=>' Modello Contratto di noleggio',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_noleggio',
		'hidden'=>''
	),

		'archy_art_noleggio.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_noleggio.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_noleggio.id','archy_art_noleggio.noleggio_title','archy_art_noleggio.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;
        $extra_button='';
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/noleggio/noleggio";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/noleggio/".$id."_noleggio.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_noleggio.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);