<?php

$configPlaceholders = [

[
'title' => 'Codici Identificativi',
'content_opt_group' => [

 'tipologia_scheda' => ['Tipologia scheda','getArrayValues'],
 'numero_archiviazione' => 'Numero di archiviazione',
 'codice' => 'Numero di inventario assegnato',
 'serie' => ['Serie','format_single_select','nome_serie','archy_art_series'],
 'collezione' => ['Collezione','format_single_select', 'nome_collezione', 'archy_art_collections'],
 'numero_elementi' => 'Numero di elementi',
 'presente_in_archivio' => ['Opera presente in archivio','format_checkbox'],
 'collocazione_geografica' => 'Collocazione in archivio',
 'precedenti_archiviazioni' => 'Numerazione da eventuali precedenti archiviazioni',
 'certificato_archiviazione' => ['Certificato di archiviazione','renderCloudImg'],
 'riferimento_catalogo_ragionato' => 'Riferimento al catalogo ragionato',
 'note_libere' => ['Note libere','format_editor'],
 'tags' => ['Tags','format_tags']
]
],

[
'title' => 'Dati Identificativi',
'content_opt_group' => [

 'nominativo_autore' => 'Autore',
 'data_nascita_autore' => ['Data di nascita autore','format_date'],
 'luogo_nascita_autore' => 'Luogo di nascita autore',
 'data_morte_autore' => ['Data morte autore','format_date'],
 'luogo_morte_autore' => 'Luogo morte autore',

 'autori' => ['Autori','format_multiple_select','name_author','archy_art_authors'],
 'collaboratori' => ['Altri autori','format_editor'],

 'titolo_opera' => 'Titolo opera',
 'altri_titoli' => ['Altri titoli','format_editor'],

 'courtesy' => 'Courtesy',
 'didascalia_courtesy' => 'Didascalia di courtesy',
 'didascalia_foto' => 'Didascalia foto',

 'tipologia_opera' => ['Tipologia opera','getArrayValues'],
 'tipologia_documenti' => ['Tipologia documenti','getArrayValues'],
 'tipologia_fonte_bibliografica' => ['Tipologia fonte bibliografica','getArrayValues'],
 'tecnica_fotografica' => ['Tecnica fotografica','getArrayValues'],

 'data_di_scatto' => ['Data di scatto','format_date'],
 'data_di_stampa' => ['Data di stampa','format_date'],

 'stato_stampa' => 'Stato di stampa',
 'titolo_volume' => 'Titolo volume',
 'numero_volume' => 'Numero volume',
 'titolo_periodico' => 'Titolo periodico',
 'numerazione_periodico' => 'Numerazione periodico',

 'titolo_mostra' => 'Titolo mostra',
 'curatore_mostra' => 'Curatore mostra',
 'sede_mostra' => 'Sede mostra',
 'citta_mostra' => 'Città mostra',
 'stato_mostra' => 'Nazione mostra',
 'date_mostra' => 'Data della mostra',

 'altra_tipologia' => 'Altra tipologia',
 'descrizione_fonte_bibliografica' => ['Descrizione fonte bibliografica','format_editor'],

 'data' => 'Anno',
 'data_specifica' => ['Data Precisa','format_date'],
 'data_pubblicazione' => ['Data Pubblicazione','format_date'],
 'data_inizio_realizzazione' => ['Data inizio realizzazione','format_date'],
 'data_fine_realizzazione' => ['Data fine realizzazione','format_date'],
 'ulteriori_datazioni' => ['Ulteriori datazioni','format_editor'],

 'luogo_produzione_opera' => 'Luogo di produzione opera',
 'geolocalizzazione' => 'Geolocalizzazione',

 'opera_edizione' => ['Opera unica - Edizione','getArrayValues'],
 'numero_edizione' => 'Numero edizione',
 'editore' => 'Editore',
 'stampatore' => 'Stampatore',

 'foto_ufficiale' => ['Foto ufficiale','renderCloudImg','scheda_catalogo-foto_ufficiale',''],
 'foto_ufficiale_secondaria' => ['Foto ufficiale secondaria','renderCloudImg','scheda_catalogo-foto_ufficiale_secondaria','']

]
],

[
'title' => 'Committenza',
'content_opt_group' => [

 'committenza' => 'Committenza',
 'curatore' => 'Curatore',
 'finanziatore' => 'Finanziatore'

]
],

[
'title' => 'Descrizione',
'content_opt_group' => [

 'tipo_di_oggetto' => ['Titolo di oggetto','getArrayValues'],
 'supporto' => ['Supporto','format_tags'],
 'materiali' => ['Materiali','format_tags'],
 'tecnica' => ['Tecnica','format_tags'],
 'dimensioni' => 'Dimensioni',

 'unita_di_misura_altezza' => ['Unita di misura','getArrayValues'],

 'dimensioni_altezza' => 'Dimensioni altezza',
 'dimensioni_lunghezza' => 'Dimensioni lunghezza',
 'numero_pagine' => 'Numero pagine',
 'durata' => 'Durata',

 'dimensioni_profondita' => 'Dimensioni profondità',
 'dimensioni_spessore' => 'Dimensioni spessore',
 'dimensioni_ingombro' => 'Dimensioni ingombro',
 'dimensioni_diametro' => 'Dimensioni diametro',

 'altre_misure' => ['Altre misure','format_editor'],

 'peso' => 'Peso',

 'firma' => ['Firma','getArrayValues'],

 'posizione_firma' => 'Posizione firma',

 'segni_particolari' => ['Segni particolari','format_editor'],
 'descrizione_retro' => ['Descrizioni retro','format_editor'],
 'descrizione_narrativa' => ['Descrizione narrativa','format_editor'],
 'indicazioni_specifiche_contestuali' => ['Indicazioni delle essenzialità specifiche o contestuali dell’opera','format_editor'],
 'site_specific' => ['Site specific','format_editor'],
 'time_specific' => ['Time specific','format_editor'],
 'altre_condizioni' => ['Altre condizioni essenziali','format_editor'],
 'istruzioni_allestimento' => ['Istruzioni allestimento','format_editor'],

 'cinquanta_anni' => ['+50 anni','format_checkbox'],
 'settanta_anni' => ['+70 anni','format_checkbox']

]
],

[
    'title' => 'Conservazione, allestimento, Esp. e Movimentazione',
    'content_opt_group' => [
        'stato_conservazione' => ['Stato di conservazione','getArrayValues'],
        'interventi_restauri' => ['Interventi e restauri','format_editor'],
        'diagnostica' => ['Diagnostica','format_editor'],
        'indicazioni_conservazione' => ['Eventuali indicazioni per la conservazione','format_editor'],
        'indicazioni_allestimento' => ['Indicazioni per allestimento','format_editor'],
        'indicazioni_imballaggio' => ['Indicazioni per imballaggio','format_editor'],
        'indicazioni_condizioni_ambientali' => ['Indicazioni condizioni ambientali','format_editor'],
        'sicurezza_spazi_espositivi' => ['Sicurezza spazi espositivi','format_editor'],
        'sicurezza_depositi' => ['Sicurezza depositi','format_editor'],
        'ingombro_imballo' => ['Ingombro imballaggio','format_editor'],
        'contatti' => ['Contatti di riferimento','format_editor'],
        'link_intervista' => ['Link intervista'],
        'file_fotografico' => ['File fotografici','renderCloudImg'],
        'condition_report' => ['Condition report','renderCloudImg']
]
],

[
    'title' => 'Provenienza, condizione, circ. e stima',
    'content_opt_group' => [
        'titolarita_attuale' => ['Titolarita attuale','format_editor'],
        'storico_provenienza' => ['Storico della provenienza','format_editor'],
        'titolo_proprieta' => ['Titolo proprietà'],
        'tipologia_proprieta' => ['Tipologia proprietà'],
        'data_acquisizione' => ['Data acquisizione','format_date'],
        'licenze_attestati_circolazione' => ['Sussistenza licenze e attestati di circolazione','format_editor'],
        'stima' => ['Stima'],
        'valuta' => ['Valuta'],
        'data_di_stima' => ['Data di stima','format_date'],
        'ulteriori_informazioni' => ['Ulteriori informazioni','format_editor'],
        'valore_assicurativo' => ['Valore Assicurativo'],
        'certificato_assicurativo' => ['Certificato Assicurativo','renderCloudImg']
]
],

[
    'title' => 'Diritti di utilizzazione economica',
    'content_opt_group' => [
        'diritti_immagine' => ['Diritti di immagine'],
        'diritti_patrimoniali' => ['Diritti patrimoniali'],
        'ulteriori_diritti' => ['Sussistenza ulteriori diritti','format_editor']
]
],

[
    'title' => 'Fonti e documenti di rif.',
    'content_opt_group' => [
        'documentazione_fotografica' => ['Documentazione fotografica','renderCloudImg'],
        'descrizione_documentazione_fotografica' => ['Descrizione documentazione fotografica','format_editor'],
        'documentazione_grafica' => ['Documentazione grafica','renderCloudImg'],
        'descrizione_documentazione_grafica' => ['Descrizione documentazione grafica','format_editor'],
        'documentazione_video' => ['Documentazione video','renderCloudImg'],
        'descrizione_documentazione_video' => ['Descrizione documentazione video','format_editor'],
        'documentazione_audio' => ['Documentazione audio','renderCloudImg'],
        'descrizione_documentazione_audio' => ['Descrizione documentazione audio','format_editor'],
        'fonti_documenti' => ['Fonte documenti','format_editor'],
        'bibliografia' => ['Bibliografia','format_editor'],
        'storia_espositiva' => ['Storia espositiva','format_editor'],
        'aste' => ['Aste','format_editor'],
        'notizie_storiche' => ['Ulteriori notizie storico – critiche','format_editor'],
        'intervista' => ['Intervista','format_editor'],
        'pubblicazioni' => ['Pubblicazioni','format_multiple_select','titolo_pubblicazione','archy_art_publications'],
        'esposizione' => ['Esposizioni','format_multiple_select','nome_esposizione','archy_art_exhibitions']
]
],

[
    'title' => 'Collegamenti',
    'content_opt_group' => [
        'documenti_progettuali' => ['Documenti progettuali','format_editor'],
        'link_esterni' => ['Link esterni','format_editor']
]
],

[
    'title' => 'Compilazione',
    'content_opt_group' => [
        'compilatore' => ['Compilatore'],
        'data_compilazione' => ['Data compilazione','format_date'],
        'data_revisione_compilazione' => ['Data revisione compilazione','format_date'],
        'status' => ['Status','getArrayValues'],
        'avviso_sospensione' => ['Avviso di sospensione','format_editor'],
        'diniego_archiviazione' => ['Diniego di archiviazione','format_editor']
]
]
    
];
