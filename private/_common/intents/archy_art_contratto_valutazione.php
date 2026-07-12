<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_contratto_valutazione';//nome tabella data-base
$intent->title='Contratto di valutazione';//titolo (plurale)
$intent->singular='Contratto di valutazione';//titolo (singolare)
$intent->add='Nuovo Contratto di valutazione';//testo usato nei forms e nei pulsanti
$intent->view_title='Contratto di valutazione: archy_art_contratto_valutazione.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_models'=>'archy_art_contratto_valutazione.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_contratto_valutazione.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
         'archy_art_contratto_valutazione.contratto_valutazione_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
		'archy_art_contratto_valutazione.model_id'=>array(
		'label'=>'Modello Contratto',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_contratto_valutazione',
		'required'=>true,
		'grid_search_type'=>'search',
		//'hidden'=>''
	),
	
		'archy_art_contratto_valutazione.data'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		//'hidden'=>''
	),
	
		'archy_art_contratto_valutazione.nominativo_ente'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.indirizzo_ente'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.cap_ente'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.comune_ente'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.provincia_ente'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.stato_ente'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'default_value'=>@$defaults->stato_certificatore,
		'hidden'=>''
	),
	

		'archy_art_contratto_valutazione.cod_fiscale_ente'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.p_iva_ente'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),

	
	'archy_art_contratto_valutazione.tipologia_documento_ente'=>array(
		'label'=>'Tipologia Documento',
		'default_value'=>@$defaults->telefono_certificatore,
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.numero_documento_ente'=>array(
		'label'=>'Numero Documento',
		'default_value'=>@$defaults->tipologia_documento_certificatore,
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_contratto_valutazione.telefono_ente'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'default_value'=>@$defaults->numero_documento_certificatore,
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.fax_ente'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'default_value'=>@$defaults->fax_certificatore,
		'hidden'=>''
	),


		'archy_art_contratto_valutazione.email_ente'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.pec_ente'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'default_value'=>@$defaults->pec_certificatore,
		'hidden'=>''
	),

	
	/*  Controparte */
	
		'archy_art_contratto_valutazione.nominativo_controparte'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.indirizzo_controparte'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.cap_controparte'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.comune_controparte'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.provincia_controparte'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.stato_controparte'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_contratto_valutazione.cod_fiscale_controparte'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.p_iva_controparte'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
	'archy_art_contratto_valutazione.tipologia_documento_controparte'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.numero_documento_controparte'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_contratto_valutazione.telefono_controparte'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.fax_controparte'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_contratto_valutazione.email_controparte'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.pec_controparte'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	/*   Rappresentante   */
	
	
		'archy_art_contratto_valutazione.nominativo_rappresentante'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.indirizzo_rappresentante'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.cap_rappresentante'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.comune_rappresentante'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.provincia_rappresentante'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.stato_rappresentante'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'hidden'=>''
	),
	

		'archy_art_contratto_valutazione.cod_fiscale_rappresentante'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.p_iva_rappresentante'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'hidden'=>''
	),

	
	'archy_art_contratto_valutazione.tipologia_documento_rappresentante'=>array(
		'label'=>'Tipologia Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.numero_documento_rappresentante'=>array(
		'label'=>'Numero Documento',
		'type'=>'text',
		'hidden'=>''
	),
	
		'archy_art_contratto_valutazione.telefono_rappresentante'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.fax_rappresentante'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),


		'archy_art_contratto_valutazione.email_rappresentante'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.pec_rappresentante'=>array(
		'label'=>'Pec',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	/* DATI CONTRATTO */
	
	
	'archy_art_contratto_valutazione.scopo_archivio'=>array(
		'label'=>'Scopo dell\'Archivio',
		'type'=>'text',
		'default_value'=>@$defaults->scopo_archivio,
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.nome_artista'=>array(
		'label'=>'Nome Artista',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.titolo_opera'=>array(
		'label'=>'Titolo Opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.anno_opera'=>array(
		'label'=>'Anno dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.tecnica_dimensioni'=>array(
		'label'=>'Tecnica e dimensioni',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.proprieta_opera'=>array(
		'label'=>'Proprietà dell\'opera',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.somma_per_valutazione'=>array(
		'label'=>'Importo richiesto per la valutazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.penale'=>array(
		'label'=>'Importo giornaliero penale',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.giorni_comunicazione'=>array(
		'label'=>'Giorni previsti per la comunicazione di fine valutazione',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.scientifico_autentiche'=>array(
		'label'=>'Comitato scientifico o Comitato per le autentiche',
		'type'=>'text',
		'default_value'=>@$defaults->scientifico,
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.importo_forfettario'=>array(
		'label'=>'Importo forfettario a carico dell\'archivio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.sede_adr'=>array(
		'label'=>'Sede ADR',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	'archy_art_contratto_valutazione.foro'=>array(
		'label'=>'Foro Competente',
		'type'=>'text',
		'default_value'=>@$defaults->foro_competente,
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.presidente'=>array(
		'label'=>'Nominativo Presidente',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_presidente,
		'hidden'=>''
	),

	
		'archy_art_contratto_valutazione.luogo'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
	//	'hidden'=>''
	),

		'archy_art_contratto_valutazione.model_template'=>array(
		'label'=>' Modello Contratto di valutazione',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_contratto_valutazione',
		'hidden'=>''
	),
	
	'archy_art_contratto_valutazione.autorizzazione_ente'=>array(

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

		'archy_art_contratto_valutazione.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_contratto_valutazione.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_contratto_valutazione.id','archy_art_contratto_valutazione.contratto_valutazione_title','archy_art_contratto_valutazione.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/contratto_valutazione/contratto_valutazione";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/contratto_valutazione/".$id."_contratto_valutazione.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_contratto_valutazione.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);