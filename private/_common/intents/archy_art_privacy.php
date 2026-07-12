<?php

$defaults=$app->get_settings($domain='impostazioni_comitato');
$intent=new stdClass();
$intent->table='archy_art_privacy';//nome tabella data-base
$intent->title='Documento di Privacy';//titolo (plurale)
$intent->singular='Documento di Privacy';//titolo (singolare)
$intent->add='Nuovo Documento di Privacy';//testo usato nei forms e nei pulsanti
$intent->view_title='Documento di Privacy: archy_art_privacy.privacy_title';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_models'=>'archy_art_privacy.model_id=archy_models.id'
];
$intent->columns=array(//colonne della tabella principale

		'archy_art_privacy.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
        'archy_art_privacy.privacy_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),
        'archy_art_privacy.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
                'lookup_filter'=>'model_intent|archy_art_privacy',
		'required'=>true,
		'grid_search_type'=>'search',
		'hidden'=>''
	),
	
		'archy_art_privacy.privacy_date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),

		'archy_art_privacy.privacy_entity_name'=>array(
		'label'=>'Nominativo',
		'type'=>'text',
		'default_value'=>@$defaults->nominativo_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_entity_address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'default_value'=>@$defaults->indirizzo_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_entity_zip'=>array(
		'label'=>'Cap',
		'type'=>'text',
		'default_value'=>@$defaults->cap_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_entity_city'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'default_value'=>@$defaults->comune_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_entity_province'=>array(
		'label'=>'Provincia',
		'type'=>'text',
		'default_value'=>@$defaults->provincia_certificatore,
		'hidden'=>''
	),
	

		'archy_art_privacy.privacy_entity_cf'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'default_value'=>@$defaults->cod_fisc_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_entity_vat'=>array(
		'label'=>'Partita Iva',
		'type'=>'text',
		'default_value'=>@$defaults->p_iva_certificatore,
		'hidden'=>''
	),



		'archy_art_privacy.privacy_entity_email'=>array(
		'label'=>'E-Mail',
		'type'=>'text',
		'default_value'=>@$defaults->email_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_data_manager'=>array(
		'label'=>'Responsabile dei dati',
		'type'=>'text',
		'default_value'=>@$defaults->responsabile_dati,
		'hidden'=>''
	),

		'archy_art_privacy.privacy_place'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		'default_value'=>@$defaults->luogo_certificatore,
		'hidden'=>''
	),

		'archy_art_privacy.model_template'=>array(
		'label'=>' Modello Documento di Privacy',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_art_privacy',
		'hidden'=>''
	),

		'archy_art_privacy.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),

		'archy_art_privacy.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(
	'archy_art_privacy.id','archy_art_privacy.privacy_title','archy_art_privacy.privacy_date'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/privacy/privacy";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/privacy/".$id."_privacy.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span></a> Scarica allegato</li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_privacy.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);
