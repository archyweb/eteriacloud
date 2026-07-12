<?php

$defaults = $app->get_settings($domain = 'impostazioni_comitato');
$intent = new stdClass();
$intent->table = 'archy_art_certificates';//nome tabella data-base
$intent->title = 'Certificati';//titolo (plurale)
$intent->singular = 'Certificato';//titolo (singolare)
$intent->add='Nuovo Certificato';//testo usato nei forms e nei pulsanti
$intent->view_title='Certificato: archy_art_certificates.certificate_title';
$intent->joins=[
    'archy_art_catalog'=>'archy_art_certificates.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_certificates.model_id=archy_models.id'
];

$intent->columns=array(
	'archy_art_certificates.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
    ),
    'archy_art_certificates.certificate_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
	
	'archy_art_certificates.model_id'=>array(
		'label'=>'Tipologia di Certificato',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_certificates',
		'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),
	
	'archy_art_certificates.opera_id'=>array(
		'label'=>'Seleziona Opera',
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.titolo_opera',
		//'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),
	
			'archy_art_certificates.numero_archiviazione_certificato'=>array(
		'label'=>'Numero Archiviazione',
		'type'=>'text',
		//'hidden'=>''
	),
	
	
	'archy_art_certificates.data_certificato'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'required'=>true,
		//'hidden'=>''
	),
    'archy_art_certificates.luogo_certificato'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
		//'hidden'=>''
	),



    /* INIZIO ENTE */
    'archy_art_certificates.nominativo_ente'=>array(
        'label'=>'Nominativo',
        'type'=>'text',
        'default_value'=>@$defaults->nominativo_certificatore,
        //'hiddenn'=>''
    ),
    'archy_art_certificates.indirizzo_ente'=>array(
        'label'=>'Indirizzo',
        'type'=>'text',
        'default_value'=>@$defaults->indirizzo_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.cap'=>array(
        'label'=>'Cap',
        'type'=>'text',
        'default_value'=>@$defaults->cap_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.comune'=>array(
        'label'=>'Comune',
        'type'=>'text',
        'default_value'=>@$defaults->comune_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.provincia'=>array(
        'label'=>'Provincia',
        'type'=>'text',
        'default_value'=>@$defaults->provincia_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.cod_fiscale_ente'=>array(
        'label'=>'Codice Fiscale',
        'type'=>'text',
        'default_value'=>@$defaults->cod_fisc_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.partita_iva_ente'=>array(
        'label'=>'Partita Iva',
        'type'=>'text',
        'default_value'=>@$defaults->p_iva_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.documento_riconoscimento'=>array(
        'label'=>'Tipologia Documento',
        'default_value'=>@$defaults->tipologia_documento_certificatore,
        'type'=>'text',
        //'hidden'=>''
    ),
    'archy_art_certificates.numero_documento_riconoscimento'=>array(
        'label'=>'Numero Documento',
        'default_value'=>@$defaults->numero_documento_certificatore,
        'type'=>'text',
        //'hidden'=>''
    ),
    'archy_art_certificates.telefono_archivio'=>array(
        'label'=>'Telefono',
        'type'=>'text',
        'default_value'=>@$defaults->telefono_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.email_ente'=>array(
        'label'=>'E-Mail',
        'type'=>'text',
        'default_value'=>@$defaults->email_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.pec_archivio'=>array(
        'label'=>'Pec',
        'type'=>'text',
        'default_value'=>@$defaults->pec_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.web_site_ente'=>array(
        'label'=>'Web site',
        'type'=>'text',
        'default_value'=>@$defaults->web_site_certificatore,
        //'hidden'=>''
    ),
    'archy_art_certificates.archivio_autore'=>array(
		'label'=>'Archivio o Autore',
		'type'=>'text',
		'default_value'=>@$defaults->archivio_artista,
		//'hidden'=>''
	),
	'archy_art_certificates.dichiarazione_autenticita'=>array(
		'label'=>'Dichiarazione di autenticità',
		'type'=>'text',
		'default_value'=>@$defaults->dichiarazione_autenticita,
		//'hidden'=>''
	),
    // FINE ENTE       
    
	'archy_art_certificates.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_certificates.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
    'archy_art_certificates.id', 'archy_art_certificates.certificate_title'
);


$intent->extra_buttons=function($id,$row){
    global $app;
    
    $physical_path=$app->subdomain_root;        
    $extra_button='';

    // PDF STANDARD	
    $new_file_path = "/assets/documents/certificati/certificato_archiviazione";
    
    $new_file_path .= "_".$id.".pdf";
    if (file_exists($physical_path.$new_file_path)) {
        $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"title="Certificato"><span class="far fa-file-archive"></span> Certificato generico</a></li>';
       // $extra_button.=' <a class="btn btn-outline-secondary" href="'.$new_file_path.'" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Certificato"><span class="far fa-file-archive"></span></a>';
    }else{
        $file_path="/assets/documents/certificati/".$id."_certificato_archiviazione.pdf";
        if (file_exists($physical_path.$file_path)) {
            $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank" title="Certificato con grafica"><span class="far fa-file-archive"></span> Certificato generico</a></li>';
            //$extra_button.=' <a class="btn btn-outline-secondary" href="'.$file_path.'" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Certificato"><span class="far fa-file-archive"></span></a>';
        }
    }    
    
    // PDF STANDARD CON SFONDO
    $new_file_path = "/assets/documents/certificati/certificato";
  
    $new_file_path .= "_".$id.".pdf";
    if (file_exists($physical_path.$new_file_path)) {
            $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Certificato con grafica</a></li>';
    }else{
            $file_path="/assets/documents/certificati/".$id."_certificato.pdf";
            if (file_exists($physical_path.$file_path)) {
                   $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Certificato con grafica</a></li>';
            }
    }
    return $extra_button;
};

$intent->include_in_form='intents/includes/art_certificates.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);