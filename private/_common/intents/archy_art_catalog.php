<?php

$defaults = $app->get_settings($domain = 'impostazioni_comitato');
$intent = new stdClass();
$intent->table = 'archy_art_catalog'; //nome tabella data-base
$intent->title = 'Scheda Catalogo'; //titolo (plurale)
$intent->singular = 'Scheda Catalogo'; //titolo (singolare)
$intent->add = 'Nuova Scheda Catalogo'; //testo usato nei forms e nei pulsanti
$intent->restore_button_in_grid=true;
$intent->view_title = 'Scheda Catalogo: archy_art_catalog.id';
$intent->joins = [
    'archy_models'=>'archy_art_catalog.model_id=archy_models.id',
    'archy_art_series'=>'archy_art_catalog.serie=archy_art_series.id',
    'archy_art_collections'=>'archy_art_catalog.collezione=archy_art_collections.id',
    'archy_art_exhibitions'=>'archy_art_catalog.esposizione=archy_art_exhibitions.id',
    'archy_art_publications'=>'archy_art_catalog.pubblicazioni=archy_art_publications.id'
];

/*MOSTRA LE COLONNE DEI CAMPI AGGIUNTIVI*/
$intent->enable_additional_fields_columns = true;
/*FINE*/


$intent->columns = array(
    'archy_art_catalog.id' => array(
        'label'=>'ID',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.didascalia_courtesy' => array(
        'label'=>'Didascalia completa di courtesy',
        'type'=>'text',
        'help_block'=>'Didascalia completa di courtesy',
        'hidden'=>''
    ),
    'archy_art_catalog.courtesy' => array(
        'label'=>'Courtesy',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.didascalia_foto' => array(
        'label'=>'Didascalia foto',
        'type'=>'text',
        'help_block'=>'Foto credits, courtesy e didascalia della fotografia ufficiale dell’Opera',
        'hidden'=>''

    ),
    'archy_art_catalog.indicazioni_allestimento' => array(
        'label'=>'Indicazioni di allestimento',
        'type'=>'editor',
        'help_block'=>'Eventuali indicazioni per l’allestimento, l’attivazione e l’esposizione',
        'hidden'=>''
    ),
    'archy_art_catalog.indicazioni_imballaggio' => array(
        'label'=>'Indicazioni per l’imballaggio',
        'type'=>'editor',
        'help_block'=>'Eventuali indicazioni per l’imballaggio, il disimballaggio, la movimentazione e il trasporto',
        'hidden'=>''
    ),
    'archy_art_catalog.indicazioni_condizioni_ambientali' => array(
        'label'=>'Indicazioni condizioni ambientali',
        'type'=>'editor',
        'help_block'=>'Eventuali indicazioni circa le condizioni ambientali e di sicurezza degli spazi espositivi e dei depositi',
        'hidden'=>''
    ),
    'archy_art_catalog.sicurezza_spazi_espositivi' => array(
        'label'=>'Sicurezza spazi espositivi',
        'type'=>'editor',
        'help_block'=>'Eventuali indicazioni circa la messa in sicurezza degli impianti espositivi',
        'hidden'=>''
    ),
    'archy_art_catalog.sicurezza_depositi' => array(
        'label'=>'Sicurezza depositi',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.titolo_proprieta' => array(
        'label'=>'Titolo di proprietà',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.diritti_immagine' => array(
        'label'=>'Diritti d\'immagine',
        'type'=>'text',
        'help_block'=>'Indicare la titolarità del diritto di sfruttamento dell’immagine dell’Opera',
        'hidden'=>''
    ),
    'archy_art_catalog.diritti_patrimoniali' => array(
        'label'=>'Diritti patrimoniali',
        'type'=>'text',
        'help_block'=>'Indica la sussistenza di ulteriori diritti patrimoniali d’autore (elencare tali diritti)',
        'hidden'=>''
    ),
    'archy_art_catalog.opera_edizione' => array(
        'label'=>'Opera unica - Edizione',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('opera_edizione'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('opera_edizione',$d);
        },
        'formatter_hide_in_form'=>'',
        //'required'=>true,
        'hidden'=>''
    ),
    'archy_art_catalog.numero_edizione' => array(
        'label'=>'Numero edizione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.curatore' => array(
        'label'=>'Nominativo del curatore',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.finanziatore' => array(
        'label'=>'Finanziatore',
        'type'=>'text',
        'hidden'=>''
    ),
    
    'archy_art_catalog.stato_stampa' => array(
        'label'=>'Stato di stampa',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.status' => array(
        'label'=>'Status',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('status'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('status',$d);
        },
        'formatter_hide_in_form'=>'',
        //'required'=>true,
        'hidden'=>''
    ),
    'archy_art_catalog.avviso_sospensione' => array(
        'label'=>'Avviso di sospensione della valutazione',
        'type'=>'editor',

        'hidden'=>''
    ),
    'archy_art_catalog.diniego_archiviazione' => array(
        'label'=>'Diniego di archiviazione',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.richiesta_aggiuntiva' => array(
        'label'=>'Richiesta di ulteriori analisi e ricerche',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.tipologia_scheda' => array(
        'label'=>'Tipo di scheda',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('tipologia_scheda'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('tipologia_scheda',$d);
        },
        'formatter_hide_in_form'=>'',
        'default_value' => '1',       
        'required' => true,
        //'hidden'=>''
    ),
    'archy_art_catalog.model_id' => array(
        'label'=>'Modello stampa pdf',
        'type'=>'search',
        'intent'=>'archy_models',
        'lookup_text'=>'archy_models.model_title',
        'lookup_filter'=>'model_intent|archy_art_catalog',
        'required' => true,
        'grid_search_type'=>'search',
        //'help_block'=>'Help: cerca',
        //'hidden'=>''
    ),
    'archy_art_catalog.codice' => array(
        'label'=>'Numero di inventario assegnato',
        'type'=>'text',		
	'help_block'=>'Numero di inventario assegnato',
        'hidden'=>''
    ),	
	'archy_art_catalog.relazione_altre_opere' => array(
        'label'=>'Opere collegate',
        'type'=>'text',
	'help_block'=>'Indicare il numero di archiviazione delle opere collegate',
        'hidden'=>''
    ),	
	
	
		'archy_art_catalog.serie' => array(
    'label'=>'Serie',
    'type'=>'select',
    'search_type'=>'text',
    'search_db'=>'archy_art_series.nome_serie',
    'options_type'=>'sql',
    'options'=>'select ID as option_value, nome_serie as option_text from archy_art_series order by option_text',
    'formatter' => function ($d, $row) {
        return App::format_single_select($d, 'nome_serie', 'archy_art_series');
    },
    'formatter_hide_in_form' => true,
    'formatter_hide_in_view' => true,
    'hidden'=>''
),
	
	'archy_art_catalog.collezione' => array(
    'label'=>'Collezione',
    'type'=>'select',
    'search_type'=>'text',
    'search_db'=>'archy_art_collections.nome_collezione',
    'options_type'=>'sql',
    'options'=>'select ID as option_value, nome_collezione as option_text from archy_art_collections order by option_text',
    'formatter' => function ($d, $row) {
        return App::format_single_select($d, 'nome_collezione', 'archy_art_collections');
    },
    'formatter_hide_in_form' => true,
    'formatter_hide_in_view' => true,
    'hidden'=>''
),


'archy_art_catalog.pubblicazioni' => array(
    'label'=>'Pubblicazioni',
    'type'=>'multiple',
    'search_type'=>'publications',
    'options_type'=>'sql',
    'options'=>'select ID as option_value, titolo_pubblicazione as option_text from archy_art_publications order by option_text',
    'formatter' => function ($d, $row) {
        return App::format_multiple_select($d, 'titolo_pubblicazione', 'archy_art_publications');
    },
    'formatter_hide_in_form' => true,
    'formatter_hide_in_view' => true,
    'hidden'=>''
),
/*

'archy_art_catalog.pubblicazioni' => array(
        'label'=>'Pubblicazioni',
        'type'=>'multiple',
        'options_type'=>'sql',
        'options'=>'select ID as option_value, titolo_pubblicazione as option_text, data_pubblicazione as option_text_desc from archy_art_publications order by option_text',
        'formatter' => function ($d, $row) {
            return App::format_multiple_select($d,'titolo_pubblicazione','archy_art_publications');
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_view' => true,
        'hidden'=>''
    ),
	
	
	    'archy_art_catalog.serie' => array(
        'label'=>'Serie',
        'type'=>'select',
        'options_type'=>'sql',
        'options'=>'select ID as option_value, nome_serie as option_text from archy_art_series order by option_text',
        'formatter' => function ($d, $row) {
            return App::format_single_select($d, 'nome_serie', 'archy_art_series');
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_view' => true,
        'hidden'=>''
    ),
	
	
	
    'archy_art_catalog.collezione' => array(
        'label'=>'Collezione',
        'type'=>'select',
        'options_type'=>'sql',
        'options'=>'select ID as option_value, nome_collezione as option_text from archy_art_collections order by option_text',
        'formatter' => function ($d, $row) {
            return App::format_single_select($d, 'nome_collezione', 'archy_art_collections');
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_view' => true,
        'hidden'=>''
    ),*/
    'archy_art_catalog.presente_in_archivio' => array(
        'label'=>'Opera presente in archivio',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'formatter_hide_in_form'=>'',
        'hidden'=>''
    ),
    'archy_art_catalog.collocazione_geografica' => array(
        'label'=>'Collocazione in archivio',
        'type'=>'text',
        'help_block'=>'Indicazione della collocazione, se presente in archivio anche per es. fondo',
        'hidden'=>''
    ),
    'archy_art_catalog.precedenti_archiviazioni' => array(
        'label'=>'Numerazione da eventuali precedenti archiviazioni',
        'type'=>'text',
        'help_block'=>'Specificare presso quali archivi',
        'hidden'=>''
    ),
    'archy_art_catalog.certificato_archiviazione' => array(
        'label'=>'Certificato di archiviazione',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,
        'help_block'=>'Allegare il certificato, se presente',
        'hidden'=>''
    ),
    'archy_art_catalog.riferimento_catalogo_ragionato' => array(
        'label'=>'Riferimento al catalogo ragionato',
        'type'=>'text',
        'help_block'=>'Numero progressivo presente nel catalogo ragionato',
        'hidden'=>''
    ),
    'archy_art_catalog.note_libere' => array(
        'label'=>'Note libere',
        'type'=>'editor',
        'help_block'=>'Campo storico libero. Può essere usato per annotazioni testuali non normalizzate.',
        'hidden'=>''
    ),
    'archy_art_catalog.tags' => array(
        'label'=>'Tags',
        'type'=>'tags',
        'tag_category'=>'Tag',
        'grid_search_type'=>'text',
        'search_type'=>'tags',
        'formatter' => function ($d, $row) {
            return App::format_tags($d, $row);
        },
        'formatter_hide_in_form'=>true,
        'help_block'=>'Inserire parole chiave normalizzate selezionandole dai suggerimenti o aggiungendone di nuove.',
        'hidden'=>''
    ),
	
	'archy_art_catalog.autori' => array(
    'label'=>'Altri autori',
    'type'=>'multiple',
    'search_type'=>'authors',
    'options_type'=>'sql',
    'options'=>'select ID as option_value, name_author as option_text from archy_art_authors order by option_text',
    'formatter' => function ($d, $row) {
        return App::format_multiple_select($d,'name_author','archy_art_authors');
    },
    'formatter_hide_in_form' => true,
    'formatter_hide_in_view' => true,
    'hidden'=>''
),
	
	/*
    'archy_art_catalog.autori' => array(
        'label'=>'Altri autori',
        'type'=>'multiple',
        'options_type'=>'sql',
        'options'=>'select ID as option_value, name_author as option_text from archy_art_authors order by option_text',
        'formatter' => function ($d, $row) {
            return App::format_multiple_select($d,'name_author','archy_art_authors');
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_view' => true,
        'hidden'=>''
    ),*/
	
    'archy_art_catalog.nominativo_autore' => array(
        'label'=>'Autore (predefinito)',
        'type'=>'text',
        //'required'=>true,
        'default_value' => @$defaults->nome_autore,
        'help_block'=>'Nome e cognome. Se curatore indicare tra parentesi (a cura di)',
        'hidden'=>''
    ),
    'archy_art_catalog.data_nascita_autore' => array(
        'label'=>'Data di nascita dell\'Autore (predefinito)',
        'type'=>'date',
        'default_value' => @$defaults->data_nascita_autore,
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.luogo_nascita_autore' => array(
        'label'=>'Luogo di nascita dell\'Autore (predefinito)',
        'type'=>'text',
        'default_value' => @$defaults->luogo_nascita_autore,
        'hidden'=>''
    ),
    'archy_art_catalog.data_morte_autore' => array(
        'label'=>'Data di morte dell\'Autore (predefinito)',
        'type'=>'date',
        'default_value' => @$defaults->data_morte_autore,
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.luogo_morte_autore' => array(
        'label'=>'Luogo di morte dell\'Autore (predefinito)',
        'default_value' => @$defaults->luogo_morte_autore,
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.collaboratori' => array(
        'label'=>'Collaboratori / Note',
        'type'=>'editor',
        'help_block'=>'Elencare eventuali altri autori o collaboratori (nome, cognome, data e luogo nascita e morte) e il loro ruolo.',
        'hidden'=>''
    ),
    'archy_art_catalog.titolo_opera' => array(
        'label'=>'Titolo',
        'type'=>'text',
        //'required'=>true,
        'help_block'=>'Riportare sia titolo, se fornito, per esteso compresi eventuali credits.',
        'hidden'=>''
    ),
    'archy_art_catalog.altri_titoli' => array(
        'label'=>'Altri titoli',
        'type'=>'editor',
        'help_block'=>'Titolo in forma breve per didascalie in mostra o in catalogo. Inserire elenco dei titoli alternativi specificando l’occasione (mostre, pubblicazioni, documenti) in cui sono stati attribuiti.',
        'hidden'=>''
    ),
    'archy_art_catalog.tipologia_opera' => array(
        'label'=>'Tipologia Opera',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('tipologia_opera'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('tipologia_opera',$d);
        },
        'formatter_hide_in_form'=>'',
        'hidden'=>''
    ),
    
    /****** NUOVI CAMPI *******/
    'archy_art_catalog.parti_mobili' => array(
        'label'=>'Parti mobili',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'formatter_hide_in_form'=>'',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_bozza' => array(
        'label'=>'Tipo bozza',
        'type'=>'text',
        'hidden'=>''
    ),            
    'archy_art_catalog.stato_bozza' => array(
        'label'=>'Stato bozza',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.protezione' => array(
        'label'=>'Protezione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.opera_finita_id' => array(
        'label'=>'Opera finita',
        'type'=>'search',
        'intent'=>'archy_art_catalog',
        'lookup_text'=>'archy_art_catalog.titolo_opera',
        'grid_search_type'=>'search',
        'hidden'=>''
    ), 
    'archy_art_catalog.sottotipo_computer_art' => array(
        'label'=>'Sottotipo computer art',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.formato_file' => array(
        'label'=>'Formato file',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.edizione' => array(
        'label'=>'Edizione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_corrispondenza' => array(
        'label'=>'Tipo corrispondenza',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.mittente_destinatario' => array(
        'label'=>'Mittente destinatario',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.originale_copia' => array(
        'label'=>'Originale copia',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.cornice' => array(
        'label'=>'Cornice',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.luogo_permanente_murale' => array(
        'label'=>'Luogo permanente murale',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.colore_disegno' => array(
        'label'=>'Colore disegno',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.audio_presente' => array(
        'label'=>'Audio presente',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.film_collegato_id' => array(
        'label'=>'Film collegato',
        'type'=>'search',
        'intent'=>'archy_art_catalog',
        'lookup_text'=>'archy_art_catalog.titolo_opera',
        'grid_search_type'=>'search',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_stampa_fotografia' => array(
        'label'=>'Tipo di stampa',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.superficie_origine' => array(
        'label'=>'Superficie origine',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.numero_componenti_installazione' => array(
        'label'=>'Numero componenti installazione',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.dimensioni' => array(
        'label'=>'Dimensioni',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.spazio_minimo_richiesto' => array(
        'label'=>'Spazio minimo richiesto',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.come_installazione' => array(
        'label'=>'Come installazione',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.canali' => array(
        'label'=>'Canali',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.durata_loop' => array(
        'label'=>'Durata loop',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.numero_schermi_proiezioni' => array(
        'label'=>'Numero schermi proiezioni',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.risoluzione' => array(
        'label'=>'Risoluzione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.media_presenti' => array(
        'label'=>'Media presenti',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.hardware_richiesto' => array(
        'label'=>'Hardware richiesto',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_interazione' => array(
        'label'=>'Tipo interazione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.necessita_rete' => array(
        'label'=>'Necessita rete',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.software_hardware_richiesto' => array(
        'label'=>'Software Hardware richiesto',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.data_anno' => array(
        'label'=>'Data anno',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.isbn' => array(
        'label'=>'Isbn',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.unico_originale' => array(
        'label'=>'Unico originale',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.rilegatura' => array(
        'label'=>'Rilegatura',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.funzionale' => array(
        'label'=>'Funzionale',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_multiplo' => array(
        'label'=>'Tipo multiplo',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.prove_multiplo' => array(
        'label'=>'Prove multiplo',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.categoria_design' => array(
        'label'=>'Categoria design',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.movimento_cinetico' => array(
        'label'=>'Movimento cinetico',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.alimentazione' => array(
        'label'=>'Alimentazione',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.data_luogo_prima_esecuzione' => array(
        'label'=>'Data e luogo prima esecuzione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.ripetibile' => array(
        'label'=>'Ripetibile',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.documentazione_multi' => array(
        'label'=>'Documentazione multi',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_progetto' => array(
        'label'=>'Tipo progetto',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.stato_progetto' => array(
        'label'=>'Stato progetto',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.output' => array(
        'label'=>'Output',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.piattaforma' => array(
        'label'=>'Piattaforma',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.software' => array(
        'label'=>'Software',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.build_versione' => array(
        'label'=>'Build versione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.oggetto_originario' => array(
        'label'=>'Oggetto originario',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.modifiche_ready_made' => array(
        'label'=>'Modifiche ready made',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.provenienza_note' => array(
        'label'=>'Provenienza note',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.base_scultura' => array(
        'label'=>'Base scultura',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.issn' => array(
        'label'=>'Issn',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.tecnica_stampa' => array(
        'label'=>'Tecnica di stampa',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.modalita_video' => array(
        'label'=>'Modalità video',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.derivazione' => array(
        'label'=>'Derivazione',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.stato_lastra_incisione' => array(
        'label'=>'Stato lastra incisione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.materiale_maschera' => array(
        'label'=>'Materiale maschera',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.indossabile' => array(
        'label'=>'Indossabile',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.uso_maschera' => array(
        'label'=>'Uso maschera',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.formato_3d' => array(
        'label'=>'Formato 3D',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.texture_presente' => array(
        'label'=>'Texture presente',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.rig_presente' => array(
        'label'=>'Rig presente',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.animazione_presente' => array(
        'label'=>'Animazione presente',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.motore_render' => array(
        'label'=>'Motore render',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.fps' => array(
        'label'=>'Fps',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.ambiente_generativo' => array(
        'label'=>'Ambiente generativo',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.variabile_generativo' => array(
        'label'=>'Variabile generativo',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.seed_parametri' => array(
        'label'=>'Seed parametri',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_ai' => array(
        'label'=>'Tipo AI',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.modello_ai' => array(
        'label'=>'Modello AI',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.post_produzione' => array(
        'label'=>'Post produzione',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.software_post_produzione' => array(
        'label'=>'Software post produzione',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.marker' => array(
        'label'=>'Marker',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.file_ar' => array(
        'label'=>'File ar',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.necessita_app' => array(
        'label'=>'Necessita app',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_asset' => array(
        'label'=>'Tipo asset',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.licenza_asset' => array(
        'label'=>'Licenza asset',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.blockchain' => array(
        'label'=>'Blockchain',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.contract_address' => array(
        'label'=>'Contract address',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.token_id' => array(
        'label'=>'Token id',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.piattaforma_mint' => array(
        'label'=>'Piattaforma mint',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.url_opera' => array(
        'label'=>'Url opera',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.tecnologia_web' => array(
        'label'=>'Tecnologia web',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.hosting' => array(
        'label'=>'Hosting',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.versione_dipendenze' => array(
        'label'=>'Versione dipendenze',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.setup_sound_art' => array(
        'label'=>'Setup sound art',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.spazio_richiesto_sound_art' => array(
        'label'=>'Spazio richiesto sound art',
        'type'=>'text',
        'hidden'=>''
    ),
    /****** NUOVI CAMPI *******/
                
    'archy_art_catalog.altra_tipologia' => array(
        'label'=>'Altra tipologia',
        'type'=>'text',
        'hidden'=>''
    ),
	
    'archy_art_catalog.tecnica_fotografica' => array(
        'label'=>'Tecnica Fotografica',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('tecnica_fotografica'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('tecnica_fotografica',$d);
        },
        'formatter_hide_in_form'=>'',
        'hidden'=>''
    ),
    'archy_art_catalog.tipologia_documenti' => array(
        'label'=>'Tipologia documenti',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('tipologia_documenti'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('tipologia_documenti',$d);
        },
        'formatter_hide_in_form'=>'',
        //'required'=>true,
        'hidden'=>''
    ),
    'archy_art_catalog.data_di_scatto' => array(
        'label'=>'Data di scatto',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        //'default_value'=>date('d/m/Y'),
        'hidden'=>''
    ),
    'archy_art_catalog.data_di_stampa' => array(
        'label'=>'Data di stampa',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        //'default_value'=>date('d/m/Y'),
        'hidden'=>''
    ),
    'archy_art_catalog.tipologia_fonte_bibliografica' => array(
        'label'=>'Tipologia fonte bibliografica',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('tipologia_fonte_bibliografica'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('tipologia_fonte_bibliografica',$d);
        },
        'formatter_hide_in_form'=>'',
        //'required'=>true,
        'hidden'=>''
    ),
    'archy_art_catalog.titolo_volume' => array(
        'label'=>'Titolo del volume',
        'type'=>'text',
        'hidden'=>''
    ),
	
	'archy_art_catalog.descrizione_fonte_bibliografica' => array(
        'label'=>'Descrizione fonte bibliografica',
        'type'=>'editor',
        'hidden'=>''
    ),
	
	'archy_art_catalog.numero_volume' => array(
        'label'=>'Numero del volume',
        'type'=>'text',
        'hidden'=>''
    ),
	
	
    'archy_art_catalog.titolo_periodico' => array(
        'label'=>'Titolo del periodico',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.numerazione_periodico' => array(
        'label'=>'Numero del periodico',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.data' => array(
        'label'=>'Anno',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.data_specifica' => array(
        'label'=>'Data Precisa',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.data_pubblicazione' => array(
        'label'=>'Data Pubblicazione',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.data_inizio_realizzazione' => array(
        'label'=>'Work in progress - data inizio',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        //'default_value'=>date('d/m/Y'),
        'hidden'=>''
    ),
    'archy_art_catalog.data_fine_realizzazione' => array(
        'label'=>'Work in progress - data fine',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        //'default_value'=>date('d/m/Y'),
        'hidden'=>''
    ),
    'archy_art_catalog.ulteriori_datazioni' => array(
        'label'=>'Ulteriori datazioni',
        'type'=>'editor',
        'help_block'=>'Date riportate erroneamente in altri testi o mostre.',
        'hidden'=>''
    ),
    'archy_art_catalog.luogo_produzione_opera' => array(
        'label'=>'Luogo',
        'type'=>'text',
        'help_block'=>'Per luogo si intende il luogo di produzione dell’opera',
        ////'required'=>true,
        'hidden'=>''
    ),
    'archy_art_catalog.geolocalizzazione' => array(
        'label'=>'Ubicazione',
        'type'=>'text',
        'help_block'=>'Per opere pubbliche o parte di collezioni private e pubbliche',
        'hidden'=>''
    ),
    'archy_art_catalog.edizione' => array(
        'label'=>'Edizione',
        'type'=>'text',
        'help_block'=>'Da compilare per i multipli: specificare il numero corrispondente della tiratura dell\opera.',
        'hidden'=>''
    ),
    'archy_art_catalog.editore' => array(
        'label'=>'editore',
        'type'=>'text',
        'help_block'=>'Da compilare per i multipli / edizioni',
        'hidden'=>''
    ),
    'archy_art_catalog.stampatore' => array(
        'label'=>'Stampatore',
        'type'=>'text',
        'help_block'=>'Da compilare per i multipli / edizioni',
        'hidden'=>''
    ),
    'archy_art_catalog.stima' => array(
        'label'=>'Stima',
        'type'=>'text',
        'help_block'=>'Indicazione del valore di stima, preceduta dall’indicazione della valuta, calcolato in base al valore oggettivo.',
        'hidden'=>''
    ),
    'archy_art_catalog.valuta' => array(
        'label'=>'Valuta',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.data_di_stima' => array(
        'label'=>'Data di stima',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        //	'default_value'=>date('d/m/Y'),
        'hidden'=>''
    ),
    'archy_art_catalog.ulteriori_informazioni' => array(
        'label'=>'Ulteriori informazioni su stime, prezzi di mercato:',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.foto_ufficiale' => array(
        'label'=>'Foto ufficiale',
        'type'=>'file',
        'formatter' => function ($d, $row) {            
            return App::format_grid_img($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloud=new StoreCloud();
            return $StoreCloud->getImageUrl($d);
        },
        'formatter_hide_in_form' => true,
        //'formatter_hide_in_excel' => true,
        'help_block'=>'Si intende l’immagine autorizzata dall’artista, per scopi di pubblicazione e comunicazione.',
        'file_control' => 'image/png, image/jpeg, image/gif',
        'file_target' => 'cloudImages',
        'hidden'=>''
    ),
    'archy_art_catalog.foto_ufficiale_secondaria' => array(
        'label'=>'Foto ufficiale secondaria',
        'type'=>'file',
        'formatter' => function ($d, $row) {
            return App::format_grid_img($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloud=new StoreCloud();
            return $StoreCloud->getImageUrl($d);
        },
        'formatter_hide_in_form' => true,
        'file_control' => 'image/png, image/jpeg, image/gif',
        'file_target' => 'cloudImages',
        'hidden'=>''
    ),
    'archy_art_catalog.committenza' => array(
        'label'=>'Committenza',
        'type'=>'text',
        'help_block'=>'Ente o persona fisica finanziatore',
        'hidden'=>''
    ),
    'archy_art_catalog.finanziatore' => array(
        'label'=>'Finanziatore/i',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.curatore' => array(
        'label'=>'Progetto curatoriale di:',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.tipo_di_oggetto' => array(
        'label'=>'Tipo di Oggetto',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('tipo_di_oggetto'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('tipo_di_oggetto',$d);
        },
        'formatter_hide_in_form'=>'',
        'help_block'=>'Per oggetto semplice si intende un’opera formata da un unico componente; per oggetto complesso un’opera composta da più componenti, in relazione tra di loro; per progetto o licenza di riattivazione si intende un’opera la quale essenza consiste nell’idea progettuale la cui realizzazione è solo eventuale (ad esempio un wall drawing o una performance); per opera interattiva si intende un’opera, semplice o complessa, la cui identità ed esistenza è subordinata all’attivazione o all’interazione da parte del pubblico. Indicazioni conformi ai PACTA.',
        'hidden'=>''
    ),
    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - CAMPI CONTROLLATI TAGS
    |--------------------------------------------------------------------------
    |
    | I vecchi campi testuali supporto/materiali/tecnica vengono gestiti
    | con il nuovo type => tags.
    |
    | Ogni campo usa una categoria separata in archy_tag, così i suggerimenti
    | restano puliti e non si mischiano tra loro.
    |
    */
    'archy_art_catalog.supporto' => array(
        'label'=>'Supporto',
        'type'=>'tags',
        'tag_category'=>'Supporto',
        'grid_search_type'=>'text',
        'search_type'=>'tags',
        'formatter' => function ($d, $row) {
            return App::format_tags($d, $row);
        },
        'formatter_hide_in_form'=>true,
        'help_block'=>'Specificare il tipo di supporto su cui è realizzata l’opera sia in caso di supporto analogico che digitale.',
        'hidden'=>''
    ),
    'archy_art_catalog.materiali' => array(
        'label'=>'Materiali',
        'type'=>'tags',
        'tag_category'=>'Materiali',
        'grid_search_type'=>'text',
        'search_type'=>'tags',
        'formatter' => function ($d, $row) {
            return App::format_tags($d, $row);
        },
        'formatter_hide_in_form'=>true,
        'help_block'=>'Si indichino i materiali costitutivi di ciascun componente dell’opera e, se possibile, le provenienze.',
        'hidden'=>''
    ),
    'archy_art_catalog.tecnica' => array(
        'label'=>'Tecnica',
        'type'=>'tags',
        'tag_category'=>'Tecnica',
        'grid_search_type'=>'text',
        'search_type'=>'tags',
        'formatter' => function ($d, $row) {
            return App::format_tags($d, $row);
        },
        'formatter_hide_in_form'=>true,
        'help_block'=>'Specificare la tecnica esecutiva e, se necessario, se l’opera è stata realizzata con oggetti preesistenti o con l’opera di terzi.',
        'hidden'=>''
    ),
    /*Dimensioni ultime modifiche Novembre 2019*/

    'archy_art_catalog.dimensioni_altezza' => array(
        'label'=>'Altezza',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.unita_di_misura_altezza' => array(
        'label'=>'Unità di misura',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('unita_di_misura_altezza'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('unita_di_misura_altezza',$d);
        },
        'formatter_hide_in_form'=>'',
        //'required'=>true,
        'hidden'=>''
    ),
    'archy_art_catalog.dimensioni_lunghezza' => array(
        'label'=>'Lunghezza',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.numero_pagine' => array(
        'label'=>'Numero di pagine',
        'type'=>'text',
        'help_block'=>'Per edizioni e libri d\’artista ',
        'hidden'=>''
    ),
    'archy_art_catalog.durata' => array(
        'label'=>'Durata',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.dimensioni_profondita' => array(
        'label'=>'Profondità',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.dimensioni_spessore' => array(
        'label'=>'Spessore',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.dimensioni_ingombro' => array(
        'label'=>'Ingombro',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.dimensioni_diametro' => array(
        'label'=>'Diametro',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.altre_misure' => array(
        'label'=>'Altre misure',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.peso' => array(
        'label'=>'Peso',
        'type'=>'text',
        'help_block'=>'Si indichi il peso dell’opera nel suo complesso e di ciascun componente dell’opera.',
        'hidden'=>''
    ),
    'archy_art_catalog.firma' => array(
        'label'=>'Firma',
        'type'=>'select',
        'options_type'=>'array',
        'options' => App::getArrayValues('firma'),
        'formatter' => function ($d, $row) {            
           return App::getArrayValues('firma',$d);
        },
        'formatter_hide_in_form'=>'',
        'help_block'=>'Indicare se presente la firma.',
        'hidden'=>''
    ),
    'archy_art_catalog.posizione_firma' => array(
        'label'=>'Posizione della firma',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.segni_particolari' => array(
        'label'=>'Iscrizioni/Incisioni/Segni particolari fronte e retro',
        'type'=>'editor',
        'help_block'=>'Indicare se presenti: monogrammi, iscrizioni, timbri ecc.',
        'hidden'=>''
    ),
    'archy_art_catalog.descrizione_retro' => array(
        'label'=>'Descrizione retro',
        'type'=>'editor',
        'help_block'=>'Indicare se presenti: monogrammi, iscrizioni, timbri ecc.',
        'hidden'=>''
    ),
    'archy_art_catalog.descrizione_narrativa' => array(
        'label'=>'Descrizione',
        'type'=>'editor',
        'help_block'=>'Si intende una descrizione testuale narrativa dell’opera. Questo campo è particolarmente rilevante nel caso di opere complesse e che prevedono un’interazione con lo spazio circostante (opere ambientali), laddove la fotografia e la ripresa audiovisiva non possono restituire l’aspetto dell’opera nella sua completezza. Indicazioni conformi ai PACTA',
        'hidden'=>''
    ),
    'archy_art_catalog.indicazioni_specifiche_contestuali' => array(
        'label'=>'Indicazioni delle essenzialità specifiche o contestuali dell’opera',
        'type'=>'editor',
        'help_block'=>'Si distingua tra elementi funzionali (ad. esempio materiale per l’allestimento) ed elementi considerati parte fondamentale dell\'opera e pertanto non sostituibili. Andranno specificate anche le parti effimere e/o deperibili e la loro eventuale sostituibilità. Nel caso di opere dipendenti da tecnologia si specifichi quali elementi costitutivi si intendono essenziali dell’identità estetica dell’opera e quali unicamente intesi come strumentazione necessaria alla sua attivazione. Nel caso di opere su supporto audiovisivo, si indichi inoltre quale formato è considerabile come master d’archivio. In caso di opere complesse, definire la relazione tra le parti, ovvero se una o più articolazioni dell’opera possano essere esposte o prestate indipendentemente dall\'insieme. In caso di “progetto” e “licenze di riattivazione” specificare: le dimensioni oppure i criteri per stabilire la scala dimensionale dell’opera, le modalità e materiali per la messa in opera, ed eventuali limitazioni relative ai soggetti esecutori dell’opera. In caso di “interazione con il pubblico”: specificare modalità di interazione con il pubblico. Indicazioni conformi ai PACTA.',
        'hidden'=>''
    ),
    'archy_art_catalog.site_specific' => array(
        'label'=>'Site Specific',
        'type'=>'editor',
        'help_block'=>'Specificare se opera a ubicazione unica (tale specifica esclude ogni altra ubicazione dell’opera) e modalità di fruizione. Se collocazione spaziale variabile, indicare: condizioni, modalità, relazione con il luogo e modalità di fruizione.',
        'hidden'=>''
    ),
    'archy_art_catalog.time_specific' => array(
        'label'=>'Time Specific',
        'type'=>'editor',
        'help_block'=>'Specificare modalità di temporizzazione. Indicare dimensione temporale variabile: tempi, frequenza, ritmi.',
        'hidden'=>''
    ),
    'archy_art_catalog.altre_condizioni' => array(
        'label'=>'Altre condizioni essenziali',
        'type'=>'editor',
        'help_block'=>'Indicare ad es. eventuali particolari di relazione con luce, suono, pubblico, ecc.',
        'hidden'=>''
    ),
    'archy_art_catalog.istruzioni_allestimento' => array(
        'label'=>'Istruzioni di allestimento',
        'type'=>'editor',
        'help_block'=>'Indicare le istruzioni per il montaggio e il disallestimento. Indicare inoltre le eventuali operazioni di manutenzione da effettuarsi nel periodo di esposizione dell’opera.',
        'hidden'=>''
    ),
    'archy_art_catalog.stato_conservazione' => array(
        'label'=>'Stato di conservazione',
        'type'=>'select',
        'default_value'=>'1',
        'options_type'=>'array',
        'options' => ['1'=>'Ottimo', '2'=>'Buono', '3'=>'Critico', '4'=>'Molto critico'],
        'help_block'=>'Indicare se: ottimo, buono, critico, molto critico.',
        'hidden'=>''
    ),
	'archy_art_catalog.esposizione' => array(
    'label'=>'Esposizioni',
    'type'=>'multiple',
    'search_type'=>'exhibitions',
    'options_type'=>'sql',
    'options'=>'select ID as option_value, nome_esposizione as option_text, data_esposizione as option_text_desc from archy_art_exhibitions order by option_text',
    'formatter' => function ($d, $row) {
        return App::format_multiple_select($d,'nome_esposizione','archy_art_exhibitions');
    },
    'formatter_hide_in_form' => true,
    'formatter_hide_in_view' => true,
    'hidden'=>''
),
	
	/*
    'archy_art_catalog.esposizione' => array(
        'label'=>'Esposizioni',
        'type'=>'multiple',
        'options_type'=>'sql',
        'options'=>'select ID as option_value, nome_esposizione as option_text, data_esposizione as option_text_desc from archy_art_exhibitions order by option_text',
        'formatter' => function ($d, $row) {
            return App::format_multiple_select($d,'nome_esposizione','archy_art_exhibitions');
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_view' => true,
        'hidden'=>''
    ),*/
	
	
    'archy_art_catalog.interventi_restauri' => array(
        'label'=>'Interventi e restauri',
        'type'=>'editor',
        'help_block'=>'Indicare eventuali interventi che hanno modificato la natura originaria dell’opera e/o restauri.',
        'hidden'=>''
    ),
    'archy_art_catalog.diagnostica' => array(
        'label'=>'Diagnostica',
        'type'=>'editor',
        'help_block'=>'Indicare se l’opera è stata sottoposta ad attività di indagine ed analisi scientifiche.',
        'hidden'=>''
    ),
    'archy_art_catalog.indicazioni_conservazione' => array(
        'label'=>'Eventuali indicazioni per la conservazione',
        'type'=>'editor',
        'help_block'=>'Indicazioni sulla natura dei materiali costitutivi impiegati (ivi compresi, se industriali, nome commerciale e tipologia di produzione), sull’eventuale modalità di sostituzione dei componenti deperibili o obsolescenti dell’opera, ivi compresi quelli tecnologici.',
        'hidden'=>''
    ),
    'archy_art_catalog.contatti' => array(
        'label'=>'Contatti di riferimento',
        'type'=>'editor',
        'help_block'=>'ad es. studio dell’artista, professionisti collaboratori nella produzione, fornitori.',
        'hidden'=>''
    ),
    'archy_art_catalog.link_intervista' => array(
        'label'=>'Link Intervista',
        'type'=>'text',
        'help_block'=>'Allegare eventuali interviste relative alla conservazione dell\'opera',
        'hidden'=>''
    ),
    'archy_art_catalog.file_fotografico' => array(
        'label'=>'File fotografici',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,
        //'formatter_hide_in_excel' => true,
        'file_control' => 'image/png, image/jpeg, image/gif, .pdf',
        'hidden'=>''
    ),
    'archy_art_catalog.condition_report' => array(
        'label'=>'Condition Report',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,
        'file_control' => 'image/png, image/jpeg, image/gif, .pdf',
        'hidden'=>''
    ),
    'archy_art_catalog.storico_provenienza' => array(
        'label'=>'Storico della provenienza',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.tipologia_proprieta' => array(
        'label'=>'Tipologia proprietà',
        'type'=>'text',
        'help_block'=>'Specificare se oggetto di compravendita, donazione, eredità, indicando l’anno in cui se ne è venuti in possesso',
        'hidden'=>''
    ),
    'archy_art_catalog.data_acquisizione' => array(
        'label'=>'Data di acquisizione',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.titolarita_attuale' => array(
        'label'=>'Titolarità attuale',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.licenze_attestati_circolazione' => array(
        'label'=>'Sussistenza di licenze e/o attestati di circolazione',
        'type'=>'editor',
        'help_block'=>'Indicare il tipo di licenza',
        'hidden'=>''
    ),
    'archy_art_catalog.diritto_sfruttamento_immagine' => array(
        'label'=>'Titolarità del diritto di sfruttamento dell’immagine',
        'type'=>'editor',
        'help_block'=>'Elencare chi li detiene',
        'hidden'=>''
    ),
    'archy_art_catalog.ulteriori_diritti' => array(
        'label'=>'Sussistenza di ulteriori diritti',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.documentazione_fotografica' => array(
        'label'=>'Documentazione fotografica',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloud=new StoreCloud();
            $return = $StoreCloud->getImageUrl($d);
            return $d != 'none' ? $return : '';
        },
        'formatter_hide_in_form' => true,        
        'file_control' => 'image/png, image/jpeg, image/gif',
        'file_target' => 'cloudImages',
        'help_block'=>'Si intende la documentazione fotografica dell’opera/documento in tutte le sue parti',
        'hidden'=>''
    ),
    'archy_art_catalog.descrizione_documentazione_fotografica' => array(
        'label'=>'Descrizione documentazione fotografica',
        'type'=>'editor',
        'help_block'=>'Indicare: autore, data, ente proprietario, collocazione, tipo di licenza',
        'hidden'=>''
    ),
    'archy_art_catalog.documentazione_grafica' => array(
        'label'=>'Documentazione grafica',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,
        'file_control' => 'image/png, image/jpeg, image/gif',
        'help_block'=>'Si intende la documentazione progettuale dell’opera (schizzi, bozzetti, render ecc.)',
        'hidden'=>''
    ),
    'archy_art_catalog.descrizione_documentazione_grafica' => array(
        'label'=>'Descrizione documentazione grafica',
        'type'=>'editor',
        'help_block'=>'Indicare i dati identificati della documentazione o il numero dell’eventuale scheda presente in catalogo.',
        'hidden'=>''
    ),
    'archy_art_catalog.documentazione_video' => array(
        'label'=>'Documentazione Video',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,
        'help_block'=>'Filmati, documentari ecc. eventualmente disponibili relativi all’opera.',
        'hidden'=>''
    ),
    'archy_art_catalog.descrizione_documentazione_video' => array(
        'label'=>'Descrizione documentazione Video',
        'type'=>'editor',
        'help_block'=>'Indicare: Autore, data, ente proprietario, collocazione, tipo di licenza',
        'hidden'=>''
    ),
    'archy_art_catalog.documentazione_audio' => array(
        'label'=>'Documentazione audio',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,
        'help_block'=>'Registrazioni o file audio relativi all’opera.',
        'hidden'=>''
    ),
    'archy_art_catalog.descrizione_documentazione_audio' => array(
        'label'=>'Descrizione documentazione Audio',
        'type'=>'editor',
        'help_block'=>'Indicare: Autore, data, ente proprietario, collocazione, tipo di licenza',
        'hidden'=>''
    ),
    'archy_art_catalog.fonti_documenti' => array(
        'label'=>'Fonti e documenti',
        'type'=>'editor',
        'help_block'=>'Altre fonti (lettere, email, siti web ecc.)',
        'hidden'=>''
    ),
    
    'archy_art_catalog.bibliografia' => array(
        'label'=>'Bibliografia',
        'type'=>'editor',
        'help_block'=>'Elenco elenco delle pubblicazioni in cui l’opera/documento compare o è citata.',
        'hidden'=>''
    ),
    'archy_art_catalog.storia_espositiva' => array(
        'label'=>'Storia espositiva',
        'type'=>'editor',
        'help_block'=>'Elenco delle mostre in cui l’opera/documento è stata esposta (titolo mostra, a cura di, luogo/istituzione, date).',
        'hidden'=>''
    ),
    'archy_art_catalog.aste' => array(
        'label'=>'Aste',
        'type'=>'editor',
        'help_block'=>'Indicare eventuali aste a cui l’opera ha preso parte',
        'hidden'=>''
    ),
    'archy_art_catalog.notizie_storiche' => array(
        'label'=>'Ulteriori notizie storico – critiche',
        'type'=>'editor',
        'help_block'=>'Ulteriori informazioni: riportare le principali posizioni del dibattito critico.',
        'hidden'=>''
    ),
    'archy_art_catalog.intervista' => array(
        'label'=>'Intervista',
        'type'=>'editor',
        'help_block'=>'È possibile integrare le informazioni con un’intervista all’artista e/o al curatore dell’archivio.',
        'hidden'=>''
    ),
    'archy_art_catalog.cinquanta_anni' => array(
        'label'=>'Opera con più di 50 anni',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'formatter_hide_in_form'=>'',
        'hidden'=>''
    ),
    'archy_art_catalog.settanta_anni' => array(
        'label'=>'Opera con più di 70 anni',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'formatter_hide_in_form'=>'',
        'hidden'=>''
    ),
    'archy_art_catalog.valore_assicurativo' => array(
        'label'=>'Valore assicurativo',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.documenti_progettuali' => array(
        'label'=>'Documenti progettuali',
        'type'=>'editor',
        'help_block'=>'Collegamento interno a schede di opere o documenti collegati. Indicare il numero id delle schede collegate',
        'hidden'=>''
    ),
    'archy_art_catalog.link_esterni' => array(
        'label'=>'Link esterni',
        'type'=>'editor',
        'help_block'=>'Link a siti o items esterni ritenuti rilevanti per l’opera/documento',
        'hidden'=>''
    ),
    'archy_art_catalog.data_compilazione' => array(
        'label'=>'Data compilazione della scheda',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
	
    'archy_art_catalog.data_revisione_compilazione' => array(
        'label'=>'Data revisione della scheda',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
    'archy_art_catalog.compilatore' => array(
        'label'=>'Nominativo del compilatore',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.model_template' => array(
        'label'=>' Modello Scheda Catalogo',
        'type'=>'editor',
        'editor_height'=>'400',
        'editor_purpose'=>'scheda_catalogo',
        'hidden'=>''
    ),
    
    'archy_art_catalog.numero_archiviazione' => array(
        'label'=>'Numero Archiviazione',
        'type'=>'text'
    ),
    'archy_art_catalog.scheda_pubblica' => array(
        'label'=>'Scheda pubblica',
        'type'=>'checkbox',
        'formatter' => function ($d, $row) {
            return App::checkbox($d);
        },
        'formatter_hide_in_form'=>'',
        //'hidden'=>'',
        'api_visibility' => true
    ),
    'archy_art_catalog.certificato_assicurativo' => array(
        'label'=>'Certificato assicurativo',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            return App::format_grid_multiple_files($d);
        },
        'excel_formatter'=>function($d,$row){
            $StoreCloudR2=new StoreR2Adapter();
            return $StoreCloudR2->getFileUrl($d);
        },
        'formatter_hide_in_form' => true,                
        'hidden'=>''
    ),
    'archy_art_catalog.numero_elementi' => array(
        'label'=>'Numero di elementi',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_catalog.ingombro_imballo' => array(
        'label'=>'Ingombro imballo',
        'type'=>'editor',
        'hidden'=>''
    ),
    'archy_art_catalog.created_at' => array(
        'label'=>'Data Creazione',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date_time($d);
        },
        'hidden'=>'' 
    ),
    'archy_art_catalog.updated_at' => array(
        'label'=>'Ultima modifica',
        'type'=>'date',
        'formatter' => function ($d, $row) {
            return App::latin_date_time($d);
        },
        'hidden'=>''        
    )
               
);
$intent->default_columns = array( //colonne del db da visualizzare di default nell'elenco (griglia)
    'archy_art_catalog.id', 'archy_art_catalog.status', 'archy_art_catalog.data_compilazione', 'archy_art_catalog.titolo_opera', 'archy_art_catalog.numero_archiviazione' //inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

/*
$intent->extra_buttons = function ($id, $row) {
    global $app;
    $extra_button = '';
	
	$physical_path=$app->subdomain_root;

	// Il numero di archiviazione NON deve dipendere dalle colonne visibili nella DataTable.
	// Se la colonna è presente nella riga la usiamo, altrimenti lo recuperiamo dal DB tramite ID tecnico record.
	$arch_number = '';
	if (isset($row['numero_archiviazione'])) {
		$arch_number = $row['numero_archiviazione'];
	} elseif (isset($row['archy_art_catalog___numero_archiviazione'])) {
		$arch_number = $row['archy_art_catalog___numero_archiviazione'];
	} else {
		$db = $app->get_db();
		$record_id = (int) $id;
		$res = $db->query("SELECT numero_archiviazione FROM archy_art_catalog WHERE id = '".$record_id."' LIMIT 1");
		if ($res && $obj = mysqli_fetch_object($res)) {
			$arch_number = $obj->numero_archiviazione;
		}
	}
	$arch_number = trim((string) $arch_number);
	
	// SCHEDA CATALOGO STANDARD	
	$new_file_path = "/assets/documents/scheda_catalogo/scheda_catalogo_archiviazione";
	if($arch_number!=''){$new_file_path .= "_".$arch_number;}
	$new_file_path .= "_".$id.".pdf";
	if (file_exists($physical_path.$new_file_path)) {
		$extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank"title="Scheda generica"><span class="far fa-file-archive"></span> Scheda generica</a></li>';
	}else{
		$file_path="/assets/documents/scheda_catalogo/".$id."_scheda_catalogo_archiviazione.pdf";
		if (file_exists($physical_path.$file_path)) {
			$extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank" title="Scheda generica"><span class="far fa-file-archive"></span> Scheda generica</a></li>';
		}
	}    

	$new_file_path = "/assets/documents/scheda_catalogo/scheda_catalogo";
	if($arch_number!=''){$new_file_path .= "_".$arch_number;}
	$new_file_path .= "_".$id.".pdf";
	if (file_exists($physical_path.$new_file_path)) {
		$extra_button.='<li><a class="dropdown-item" href="'.$new_file_path.'" target="_blank" title="Scheda Catalogo"><span class="far fa-file-alt"></span> Scheda Catalogo</a></li>';
	}else{
		$file_path="/assets/documents/scheda_catalogo/".$id."_scheda_catalogo.pdf";
		if (file_exists($physical_path.$file_path)) {
			$extra_button.='<li><a class="dropdown-item" href="'.$file_path.'" target="_blank" title="Scheda Catalogo"><span class="far fa-file-alt"></span> Scheda Catalogo</a></li>';
		}
        }
    
    return $extra_button;
};*/
$intent->extra_buttons = function ($id, $row) {
    global $app;

    /*
    |--------------------------------------------------------------------------
    | PULSANTI EXTRA PDF SCHEDA CATALOGO - NAMING DINAMICO PULITO
    |--------------------------------------------------------------------------
    |
    | Questa funzione deve sostituire SOLO il vecchio blocco extra_buttons.
    |
    | Non modifica:
    | - colonne DataTable;
    | - formatter dei campi aggiuntivi;
    | - upload;
    | - filtri;
    | - campi dell'intent.
    |
    | Regole naming:
    |
    | Scheda catalogo principale:
    |     nome-personalizzato_ID.pdf
    |
    | Scheda generica / uso interno:
    |     scheda_generica_nome-personalizzato_ID.pdf
    |
    | L'unico aggancio tecnico resta l'ID finale.
    |
    */

    $extra_button = '';
    $record_id = (int) $id;
	
	$extra_button .= '
		<li>
			<a class="dropdown-item"
			   href="/archy/app.php?do_action=duplicate_art_catalog&catalog_id='.$record_id.'"
			   onclick="return confirm(\'Duplicare questa scheda catalogo?\')"
			   data-bs-toggle="tooltip"
			   data-bs-placement="top"
			   title="Duplica scheda catalogo">
				<span class="fas fa-paste"></span> Duplica scheda
			</a>
		</li>';

    $physical_path = rtrim($app->subdomain_root, '/');
    $folder_relative = '/assets/documents/scheda_catalogo/';
    $folder_physical = $physical_path.$folder_relative;

    if(!is_dir($folder_physical)):
        return $extra_button;
    endif;

    /*
    |--------------------------------------------------------------------------
    | HELPER: TROVA PDF PIÙ RECENTE PER RECORD
    |--------------------------------------------------------------------------
    |
    | type:
    | - catalog = scheda catalogo graficata
    | - generic = scheda generica / uso interno
    |
    */

    $find_latest_pdf_for_record = function($record_id, $type = 'catalog') use ($folder_physical, $folder_relative) {

        $files = glob(rtrim($folder_physical, '/').'/*.pdf');

        if(empty($files)):
            return '';
        endif;

        $candidates = [];

        foreach($files as $file):

            if(!is_file($file)):
                continue;
            endif;

            $basename = basename($file);
            $lower = strtolower($basename);

            /*
            | L'ID deve essere alla fine del nome prima di .pdf.
            |
            | Esempi validi:
            | - LV-2009-019_Senza-Titolo_2457.pdf
            | - scheda_generica_LV-2009-019_Senza-Titolo_2457.pdf
            | - scheda_catalogo_0_2457.pdf
            | - scheda_catalogo_archiviazione_0_2457.pdf
            |
            */
            if(!preg_match('/(^|[_\-])'.(int)$record_id.'\.pdf$/i', $basename)):
                continue;
            endif;

            /*
            | Compatibilità:
            | - nuovo prefisso: scheda_generica_
            | - vecchi prefissi: archiviazione / archive / generica
            */
            $is_generic = (
                strpos($lower, 'scheda_generica_') === 0 ||
                strpos($lower, 'archiviazione') !== false ||
                strpos($lower, 'archive') !== false ||
                strpos($lower, 'generica') !== false
            );

            if($type == 'generic' && !$is_generic):
                continue;
            endif;

            if($type == 'catalog' && $is_generic):
                continue;
            endif;

            $candidates[] = $file;

        endforeach;

        if(empty($candidates)):
            return '';
        endif;

        /*
        | Se esistono più PDF per lo stesso record, usa quello più recente.
        */
        usort($candidates, function($a, $b){
            return filemtime($b) - filemtime($a);
        });

        return $folder_relative.basename($candidates[0]);
    };

    $extra_button .= '
<li>
    <a class="dropdown-item"
       href="/archy/app.php?intent=archy_art_catalog&do_action=open_pdf&id='.$record_id.'&type=generic"
       target="_blank">
       <span class="far fa-file-archive"></span> Scheda generica
    </a>
</li>';

$extra_button .= '
<li>
    <a class="dropdown-item"
       href="/archy/app.php?intent=archy_art_catalog&do_action=open_pdf&id='.$record_id.'&type=catalog"
       target="_blank">
       <span class="far fa-file-alt"></span> Scheda Catalogo
    </a>
</li>';

    return $extra_button;
};
$intent->include_in_view = 'intents/includes/mark_as_read.php';
//$intent->extra_action = 'create_authenticity_certificate';
$intent->include_in_form = 'intents/includes/art_catalog.php';
$intent->include_in_grid = 'intents/includes/art_catalog_filters.php'; //include i pulsanti blu di scelta - Modificare/aggiornare anche il file source.php cercando "extrawhere" ed impostando il campo del db in cui cercare/filtrare.
//$intent->extra_action = 'store_scheda_catalogo';
$intent->extra_action = 'store_manage_pdf';
$intent->include_additional_fields = true;
$app->merge_intents($intent);
