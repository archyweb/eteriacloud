<?php

$intent=new stdClass();
$intent->table='archy_art_condition_report';
$intent->title='Condition Report';
$intent->singular='Condition Report';
$intent->add='Nuovo Condition Report';
$intent->view_title='Condition Report archy_art_condition_report.id';
$intent->joins=[
    'archy_art_catalog'=>'archy_art_condition_report.opera_id=archy_art_catalog.id',
    'archy_models'=>'archy_art_condition_report.model_id=archy_models.id'
];
$intent->columns=array(
	'archy_art_condition_report.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_condition_report.condition_report_title'=>array(
		'label'=>'Titolo documento',
		'type'=>'text',
		//'default_value'=>@$defaults->nominativo_certificatore,
		//'hidden'=>''
	),
	'archy_art_condition_report.opera_id'=>array(
		'label'=>'Opera',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_art_catalog',
		'lookup_text'=>'archy_art_catalog.titolo_opera',
		//'hidden'=>''
	),
	
		'archy_art_condition_report.model_template'=>array(
		'label'=>' Modello Certificato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'editor_purpose'=>'condition_report',
		'hidden'=>''
	),
	
	'archy_art_condition_report.model_id'=>array(
		'label'=>'Modello',
		'type'=>'search',
		'intent'=>'archy_models',
		'lookup_text'=>'archy_models.model_title',
        'lookup_filter'=>'model_intent|archy_art_condition_report',
		'required'=>true,
		'grid_search_type'=>'search',
	),
	
	'archy_art_condition_report.numero'=>array(
		'label'=>'Numero registrazione',
		'type'=>'text',
		//'hidden'=>''
	),
	
	'archy_art_condition_report.data'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	
	'archy_art_condition_report.compilato_da'=>array(
		'label'=>'Compilato da',
		'type'=>'text',
		//'hidden'=>''
	),
	
	'archy_art_condition_report.luogo'=>array(
		'label'=>'Luogo',
		'type'=>'text',
		//'hidden'=>''
	),
	
	'archy_art_condition_report.doc_fotografica'=>array(
		'label'=>'Documentazione Fotografica',
		'type'=>'bulk_upload',
		'formatter'=>function($d,$row){
			return App::format_grid_multiple_files($d);
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'file_control' => 'image/png, image/jpeg, image/gif, .pdf'
		//'hidden'=>''
	),
	
	/*inizio campi STATO DI CONSERVAZIONE DELL’OPERA */
	
	'archy_art_condition_report.alterazione_cromatica_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.alterazione_cromatica_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.concrezione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.concrezione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.corrosione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.corrosione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ossidazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ossidazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.croste_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.croste_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.deformazioni_ondulazioni_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.deformazioni_ondulazioni_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.degradazione_differenziata_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.degradazione_differenziata_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.deposito_superficiale_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.deposito_superficiale_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.difetti_fusione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.difetti_fusione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.disgregazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.disgregazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.distacchi_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.distacchi_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.efflorescenza_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.efflorescenza_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.erosione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.erosione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.esfoliazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.esfoliazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.fratturazione_fessurazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.fratturazione_fessurazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.incrostazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.incrostazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.lacune_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.lacune_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.macchie_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.macchie_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.tagli_fori_lacerazioni_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.tagli_fori_lacerazioni_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.sbiadimento_opacizzazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.sbiadimento_opacizzazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.pellicola_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.pellicola_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.pitting_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.pitting_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.polverizzazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.polverizzazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.restauri_precedenti_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.restauri_precedenti_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.rigonfiamenti_sollevamento_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.rigonfiamenti_sollevamento_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.scagliatura_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.scagliatura_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.sconnessioni_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.sconnessioni_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.vandalismi_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.vandalismi_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.integrazioni_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.integrazioni_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.patina_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.patina_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.infestazione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.infestazione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ingiallimento_imbrunimento_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ingiallimento_imbrunimento_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.infezione_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.infezione_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.residui_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.residui_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.distorsioni_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.distorsioni_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.abrasioni_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.abrasioni_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.grinze_pieghe_sup_primario'=>array(
		'label'=>'Supporto Primario',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.grinze_pieghe_montaggio'=>array(
		'label'=>'Montaggio',
		'type'=>'text',
		'hidden'=>''
	),
	
	/*fine campi STATO DI CONSERVAZIONE DELL’OPERA */
	
	/*Inizio campi TRASPORTO INIZIO  */
	
	'archy_art_condition_report.ti_imballato_il'=>array(
		'label'=>'Imballato il',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_numero_contenitore'=>array(
		'label'=>'Numero identificativo contenitore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_contenitore_condizioni'=>array(
		'label'=>'Tipo di contenitore e relative condizioni',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_imballaggio_condizioni'=>array(
		'label'=>'Tipo di imballaggio e relative condizioni: ',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_tipo_riempimento'=>array(
		'label'=>'Tipo di riempimento',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_condizioni_partenza'=>array(
		'label'=>'Condizioni alla partenza',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_danni_riscontrati'=>array(
		'label'=>'Tipo di danni riscontrati',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_nome_accompagnatore'=>array(
		'label'=>'Nominativo accompagnatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.ti_nome_ricevente'=>array(
		'label'=>'Nominativo ricevente',
		'type'=>'text',
		'hidden'=>''
	),
	
	
	/*fine campi TRASPORTO INIZIO  */
	
	
	/*Inizio  campi TRASPORTO FINE  */
	
	'archy_art_condition_report.tf_data_partenza'=>array(
		'label'=>'Data di partenza',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_condition_report.tf_data_arrivo'=>array(
		'label'=>'Data di arrivo',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_condition_report.tf_disimballato_il'=>array(
		'label'=>'Disimballato il',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	
	
	'archy_art_condition_report.tf_condizioni_arrivo'=>array(
		'label'=>'Condizioni all\'arivo',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.tf_danni_riscontrati'=>array(
		'label'=>'Tipo di danni riscontrati',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.tf_nome_accompagnatore'=>array(
		'label'=>'Nominativo accompagnatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.tf_nome_ricevente'=>array(
		'label'=>'Nominativo ricevente',
		'type'=>'text',
		'hidden'=>''
	),
	
	/*fine campi TRASPORTO FINE  */
	
	
	/*Inizio  campi RICONSEGNA */
	
	'archy_art_condition_report.r_data_partenza'=>array(
		'label'=>'Data di partenza',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_condition_report.r_data_arrivo'=>array(
		'label'=>'Data di arrivo',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_condition_report.r_disimballato_il'=>array(
		'label'=>'Disimballato il',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	
	'archy_art_condition_report.r_condizioni_arrivo'=>array(
		'label'=>'Condizioni all\'arrivo',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.r_danni_riscontrati'=>array(
		'label'=>'Tipo di danni riscontrati',
		'type'=>'editor',
		'hidden'=>''
	),
	
	'archy_art_condition_report.r_nome_accompagnatore'=>array(
		'label'=>'Nominativo accompagnatore',
		'type'=>'text',
		'hidden'=>''
	),
	
	'archy_art_condition_report.r_nome_ricevente'=>array(
		'label'=>'Nominativo ricevente',
		'type'=>'text',
		'hidden'=>''
	),
	
	/*fine campi RICONSEGNA */
	
	/*Inizio  campi DOCUMENTAZIONE FOTOGRAFICA PRECEDENTE  */
	
	
	/*fine campi DOCUMENTAZIONE FOTOGRAFICA PRECEDENTE  */
	
	/*Inizio  campi DOCUMENTAZIONE FOTOGRAFICA SUCCESSIVA */
	
	
	/*fine campi DOCUMENTAZIONE FOTOGRAFICA SUCCESSIVA */

		'archy_art_condition_report.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_condition_report.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_art_condition_report.id','archy_art_condition_report.condition_report_title','archy_art_condition_report.data'
);

$intent->extra_buttons=function($id,$row){
	global $app;
        $physical_path=$app->subdomain_root;        
        $extra_button='';
        
        // PDF STANDARD CON SFONDO
        $new_file_path = "/assets/documents/condition_report/condition_report";
        $new_file_path .= "_".$id.".pdf";
        if (file_exists($physical_path.$new_file_path)) {
                $extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
        }else{
                $file_path="/assets/documents/condition_report/".$id."condition_report.pdf";
                if (file_exists($physical_path.$file_path)) {
                        $extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank"><span class="far fa-file-alt"></span> Scarica allegato</a></li>';
                }
        }
        return $extra_button;
};

$intent->include_in_form='intents/includes/art_condition_report.php';
$intent->extra_action='store_manage_pdf';
$app->merge_intents($intent);