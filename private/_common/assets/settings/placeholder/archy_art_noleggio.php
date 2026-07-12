<?php

$configPlaceholders = [

[
'title' => 'Dati Archivio',
'content_opt_group' => [

 'archivio_artista' => ['Archivio o Artista','getArrayValues'],

 'nominativo_archivio' => 'Nominativo cedente',
 'indirizzo_archivio' => 'Indirizzo cedente',
 'cap_archivio' => 'Cap cedente',
 'comune_archivio' => 'Comune cedente',
 'provincia_archivio' => 'Provincia cedente',
 'stato_archivio' => 'Nazione cedente',
 'cod_fisc_archivio' => 'Codice Fiscale cedente',
 'p_iva_archivio' => 'Partiva IVA cedente',
 'tipologia_documento_archivio' => 'Tipologia documento cedente',
 'numero_documento_archivio' => 'Numero documento cedente',
 'telefono_archivio' => 'Telefono cedente',
 'fax_archivio' => 'Fax cedente',
 'email_archivio' => 'Email cedente',
 'pec_archivio' => 'PEC cedente'

]
],

[
'title' => 'Dati Controparte',
'content_opt_group' => [

 'nominativo_controparte' => 'Nominativo controparte',
 'indirizzo_controparte' => 'Indirizzo controparte',
 'cap_controparte' => 'Cap controparte',
 'comune_controparte' => 'Comune controparte',
 'provincia_controparte' => 'Provincia controparte',
 'stato_controparte' => 'Nazione controparte',
 'cod_fisc_controparte' => 'Codice Fiscale controparte',
 'p_iva_controparte' => 'Partiva IVA controparte',
 'tipologia_documento_controparte' => 'Tipologia documento controparte',
 'numero_documento_controparte' => 'Numero documento controparte',
 'telefono_controparte' => 'Telefono controparte',
 'fax_controparte' => 'Fax controparte',
 'email_controparte' => 'Email controparte',
 'pec_controparte' => 'PEC controparte'

]
],

[
'title' => 'Dati Rappresentante',
'content_opt_group' => [

 'nominativo_rappresentante' => 'Nominativo rappresentante',
 'indirizzo_rappresentante' => 'Indirizzo rappresentante',
 'cap_rappresentante' => 'Cap rappresentante',
 'comune_rappresentante' => 'Comune rappresentante',
 'provincia_rappresentante' => 'Provincia rappresentante',
 'stato_rappresentante' => 'Nazione rappresentante',
 'cod_fisc_rappresentante' => 'Codice Fiscale rappresentante',
 'p_iva_rappresentante' => 'Partiva IVA rappresentante',
 'tipologia_documento_rappresentante' => 'Tipologia documento rappresentante',
 'numero_documento_rappresentante' => 'Numero documento rappresentante',
 'telefono_rappresentante' => 'Telefono rappresentante',
 'fax_rappresentante' => 'Fax rappresentante',
 'email_rappresentante' => 'Email rappresentante',
 'pec_rappresentante' => 'PEC rappresentante'

]
],

[
'title' => 'Oggetto del contratto',
'content_opt_group' => [

 'nome_opera' => ['Titolo dell\'Opera','custom_opera_title','opera_id','titolo_opera'],
 'valore_opera' => 'Valore dell\'Opera',
 'titolo_disposizione' => 'Titolo disposizione',
 'scopo_utilizzo' => 'Scopo di utilizzo',
 'evento_noleggio' => 'Nome dell\'evento',

 'date_evento' => ['Data dell\'evento','format_date'],
 'sede_evento' => 'Sede dell\'evento',

 'progetto_evento' => ['Tipo di progetto','getArrayMultipleValues'],

 'data_inizio' => ['Data inizio noleggio','format_date'],
 'data_fine' => ['Data fine noleggio','format_date'],

 'tacito_rinnovo' => ['Tacito rinnovo','getArrayValues'],
 'periodo_disdetta' => 'Periodo di disdetta',
 'giorni_preavviso' => 'Giorni di preavviso'

]
],

[
'title' => 'Obblighi, dichiarazioni e responsabilità',
'content_opt_group' => [

 'archivio_autore' => ['Achivio o Autore','getArrayValues'],
 'esclusiva_non_esclusiva' => ['Esclusività contrattuale','getArrayValues'],
 'originario_derivato' => ['Titolo originario derivato','getArrayValues'],
 'valore_assicurativo' => 'Valore assicurativo',
 'nome_assicurazione' => 'Nome assicurazione',
 'sede_riconsegna' => 'Sede riconsegna',
 'tempo_concessione' => 'Tempo concessione',
 'dettagli_materiali' => ['Dettagli materiali','getArrayMultipleValues'],
 'utilizzatore_archivio' => ['Utilizzatore o Archivio','getArrayValues'],
 'nome_trasportatore' => 'Nome del trasportatore'

]
],

[
'title' => 'Tutela dell’Opera',
'content_opt_group' => [

 'tipo_sorveglianza' => ['Tipo di sorveglianza','getArrayValues'],
 'descrizione_sorveglianza' => ['Descrizione sorveglianza','format_editor'],
 'esposizione_esterna' => ['Esposizione in esterna','getArrayValues']

]
],

[
'title' => 'Oneri di trasporto Opera e responsabilità Utilizzatore',
'content_opt_group' => [

 'opzione_registrar' => ['Opzione registrar','getArrayValues'],
 'individuazione_registrar' => ['Individuazione registrar','getArrayValues'],
 'nominativo_registrar' => 'Nominativo registrar',
 'email_registrar' => 'Email registrar',
 'pec_registrar' => 'Pec registrar',
 'tel_registrar' => 'Telefono registrar',
 'spese_registrar' => ['Spese registrar','getArrayValues']

]
],

[
'title' => 'Corrispettivo',
'content_opt_group' => [

 'periodicita_canone' => ['Periodicità del canone','getArrayValues'],
 'corrispettivo_noleggio' => 'Corrispettivo del noleggio',
 'esposizione_iva' => ['Esposizione iva','getArrayValues'],
 'valore_iva' => 'Importo iva',
 'giorno_pagamento' => 'Giorno del pagamento',
 'modalita_pagamento' => 'Modalità di pagamento',
 'provvigione_vendita' => ['Provvigione sulla vendita','getArrayValues'],
 'provvigione_percentuale' => 'Percentuale della provvigione',
 'sconto_canone_per_acquisto' => ['Sconto canone per acquisto','getArrayValues']

]
],

[
'title' => 'Diritti di utilizzazione economica dell’Opera',
'content_opt_group' => [

 'concessione_diritti' => ['Concessione diritti d\'immagine','getArrayValues','concessione_diritti_noleggio'],
 'diritti_immagine_corrispettivo' => 'Corrispettivo diritti d\'immagine',
 'diritto_immagine' => ['Diritti di utilizzo e riproduzione','getArrayMultipleValues'],
 'dettagli_riproduzione' => ['Dettagli riproduzione','format_editor'],
 'dicitura_opera' => 'Dicitura opera',
 'numero_copie' => 'Numero di copie'

]
],

[
'title' => 'Legge applicabile e Foro competente',
'content_opt_group' => [

 'sede_adr' => 'Sede ADR',
 'sede_foro' => 'Sede foro',
 'data' => ['Data del contratto','format_date'],
 'luogo_contratto' => 'Luogo del contratto'

]
]

];
