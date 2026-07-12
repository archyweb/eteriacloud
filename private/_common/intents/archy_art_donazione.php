<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_donazione';//nome tabella data-base
$intent->title='Contratto di donazione';//titolo (plurale)
$intent->singular='Contratto di donazione';//titolo (singolare)
$intent->add='Nuovo Contratto di donazione';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di donazione: archy_art_donazione.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_art_catalog'=>'archy_art_donazione.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_donazione.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_donazione.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	 'archy_art_donazione.donazione_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
		'archy_art_donazione.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_donazione',
		'required'=>true,
		'grid_search_type'=>'search'
	),

		'archy_art_donazione.opera_id'=>array(
		'label'=>'Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.id',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	
	'archy_art_donazione.titolo_opera'=>array(
		'label'=>'Nome dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.nominativo_autore'=>array(
		'label'=>'Nominativo Autore',
		'type'=>'text',
		//'required'=>true,
		'hidden'=>''

	),
	
	'archy_art_donazione.valore_opera'=>array(
		'label'=>'Valore dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_donazione.data'=>array(
		'label'=>'Data contratto',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	
	'archy_art_donazione.luogo'=>array(
		'label'=>'Luogo contratto',
		'type'=>'text',
		'required'=>true,
		'default_value'=>@$defaults->luogo_certificatore,
	),

	
		'archy_art_donazione.nominativo_donante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.indirizzo_donante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.cap_donante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.comune_donante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.provincia_donante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
		'archy_art_donazione.stato_donante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.cod_fisc_donante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.p_iva_donante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.tipologia_documento_donante'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.numero_documento_donante'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.telefono_donante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->telefono_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.fax_donante'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'default_value'=>@$defaults->fax_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.email_donante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),

		'archy_art_donazione.pec_donante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),
	
	'archy_art_donazione.archivio_artista'=>array(
		'label'=>'Archivio o Artista',
		'type'=>'text',
		'default_value'=>@$defaults->archivio_artista,
		'hidden'=>''
	),
	
	/*   Rappresentante   */
	
	
		'archy_art_donazione.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_donazione.cod_fisc_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
	'archy_art_donazione.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_donazione.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_donazione.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_donazione.vivente'=>array(
		'label'=>'Artista vivente o Archivio',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('vivente_donazione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('vivente_donazione',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_donazione.nominativo_donatario'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.indirizzo_donatario'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.cap_donatario'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.comune_donatario'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.provincia_donatario'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_donazione.stato_donatario'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.cod_fisc_donatario'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.p_iva_donatario'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.tipologia_documento_donatario'=>array(
		'label'=>'Tipologia Documento di riconoscimento',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.numero_documento_donatario'=>array(
		'label'=>'Numero Documento di riconoscimento',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.telefono_donatario'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.fax_donatario'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.email_donatario'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.pec_donatario'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.model_template'=>array(
		'label'=>' Modello Certificato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_donazione',
		'hidden'=>''
	),
	
	
	/*    -------------------------------------------------------   */
	
	'archy_art_donazione.titolo_proprieta_donante'=>array(
		'label'=>'Titolo proprietà donante',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.privato_pubblico'=>array(
		'label'=>'Tipo di Donatario',	
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('privato_pubblico'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('privato_pubblico',$d);
                },
		'formatter_hide_in_form'=>'',
		//'required'=>true,
		'hidden'=>''
	),
	
	'archy_art_donazione.autorizzazione_ente'=>array(

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

	
	'archy_art_donazione.rappresentante_donante'=>array(
		'label'=>'Legale Rappresentante donante',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_donazione.referente_restauro'=>array(
		'label'=>'Referente del Restauro',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.nome_archivio_artista'=>array(
		'label'=>'Nominativo Archivio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.tipo_di_cessione'=>array(
		'label'=>'Tipologia di donazione',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('tipo_di_cessione'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('tipo_di_cessione',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	'archy_art_donazione.titolarita'=>array(
		'label'=>'Titolarità donazione',
		'type'=>'select', 
		'options_type'=>'array',
                'options' => App::getArrayValues('titolarita'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('titolarita',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	
	'archy_art_donazione.spese_imballaggio'=>array(
		'label'=>'Spese Imballaggio',
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('spese_imballaggio'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('spese_imballaggio',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_donazione.titolo_disposizione'=>array(
		'label'=>'Titolo disposizione',
		'type'=>'text',
		'hidden'=>''
	),
	
	/*'archy_art_donazione.esclusiva_non_esclusiva'=>array(
		'label'=>'Esclusività contrattuale',	
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
		//'required'=>true,
		'hidden'=>''
	),*/
	
	
	'archy_art_donazione.originario_derivato'=>array(
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
	
	'archy_art_donazione.durata_cessione'=>array(
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
	
	
	'archy_art_donazione.durata_tempo'=>array(
		'label'=>'Indicare in giorni il tempo della durata di cessione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.dicitura_opera'=>array(
		'label'=>'Dicitura Opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	
	/*
	
		'additional_fields.tables'=>array(
		'label'=>'Applica alle tabelle',
		'type'=>'multiple', 
		'options_type'=>'sql',
		'options'=>'select table_name as option_value, alias as option_text from admin_additional_fields order by option_text',
		'required'=>'',
		'formatter'=>function($d,$row){return App::format_additional_fields_tables(unserialize($d));},
		'formatter_hide_in_form'=>true
	),
	*/
	'archy_art_donazione.diritti_opera'=>array(
		'label'=>'Diritti di utilizzazione',
		'type'=>'multiple', 
		'options_type'=>'array',
                'options' => App::getArrayMultipleValues('diritti_opera'),
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	
	'archy_art_donazione.dettagli_riproduzione'=>array(
		'label'=>'Dettagli di riproduzione',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_donazione.copyright_fotografico'=>array(
		'label'=>'Copyright Fotografico',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.numero_copie'=>array(
		'label'=>'Numero di copie',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_donazione.facolta_di_prestito'=>array(
		'label'=>'Facoltà di prestito',
		'type'=>'select', 
		'options_type'=>'array',
                 'options' => App::getArrayValues('facolta_di_prestito'),
                'formatter' => function ($d, $row) {            
                   return App::getArrayValues('facolta_di_prestito',$d);
                },
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	
	'archy_art_donazione.valore_assicurativo'=>array(
		'label'=>'Valore Assicurato',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.nome_assicurazione'=>array(
		'label'=>'Nominato Assicurazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.nome_trasportatore'=>array(
		'label'=>'Nominativo Trasportatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_donazione.sede_foro'=>array(
		'label'=>'Sede Foro competente',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_donazione.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_donazione.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_donazione.id','archy_art_donazione.donazione_title','archy_art_donazione.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/donazione/donazione";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/donazione/".$id."_donazione.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_donazione.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);