<?php

$configPlaceholders = [

[
'title' => 'Dati Cedente',
'content_opt_group' => [

 'nominativo_ente' => 'Nominativo',
 'indirizzo_ente' => 'Indirizzo',
 'cap_ente' => 'Cap',
 'comune_ente' => 'Comune',
 'provincia_ente' => 'Provincia',
 'cod_fisc_ente' => 'Codice Fiscale',
 'p_iva_ente' => 'Partiva IVA',
 'tipologia_documento_ente' => 'Tipologia documento',
 'numero_documento_ente' => 'Numero documento',
 'telefono_ente' => 'Telefono',
 'fax_ente' => 'Fax',
 'email_ente' => 'Email',
 'pec_ente' => 'PEC'

]
],

[
'title' => 'Dati Cessionario',
'content_opt_group' => [

 'nominativo_controparte' => 'Nominativo',
 'indirizzo_controparte' => 'Indirizzo',
 'cap_controparte' => 'Cap',
 'comune_controparte' => 'Comune',
 'provincia_controparte' => 'Provincia',
 'cod_fisc_controparte' => 'Codice Fiscale',
 'p_iva_controparte' => 'Partiva IVA',
 'tipologia_documento_controparte' => 'Tipologia documento',
 'numero_documento_controparte' => 'Numero documento',
 'telefono_controparte' => 'Telefono',
 'fax_controparte' => 'Fax',
 'email_controparte' => 'Email',
 'pec_controparte' => 'PEC'

]
],

[
'title' => 'Dati Rappresentante',
'content_opt_group' => [

 'nominativo_rappresentante' => 'Nominativo',
 'indirizzo_rappresentante' => 'Indirizzo',
 'cap_rappresentante' => 'Cap',
 'comune_rappresentante' => 'Comune',
 'provincia_rappresentante' => 'Provincia',
 'cod_fisc_rappresentante' => 'Codice Fiscale',
 'p_iva_rappresentante' => 'Partiva IVA',
 'tipologia_documento_rappresentante' => 'Tipologia documento',
 'numero_documento_rappresentante' => 'Numero documento',
 'telefono_rappresentante' => 'Telefono',
 'fax_rappresentante' => 'Fax',
 'email_rappresentante' => 'Email',
 'pec_rappresentante' => 'PEC'

]
],

[
'title' => 'Oggetto del contratto',
'content_opt_group' => [

 'titolo_opera' => 'Titolo dell\'Opera',
 'tipologia_evento' => ['Tipologia evento','format_editor'],
 'esclusiva' => ['Esclusiva','getArrayValues'],
 'durata_cessione' => ['Durata cessione','getArrayValues'],
 'durata_tempo' => 'Indicazione giorni cessione',
 'data_inizio' => ['Data inizio','format_date'],
 'data_fine' => ['Data fine','format_date'],
 'tacito_rinnovo' => ['Tacito rinnovo','getArrayValues'],
 'periodo_disdetta' => 'Periodo disdetta'

]
],

[
'title' => 'Obblighi, dichiarazioni e responsabilità',
'content_opt_group' => [

 'originario_derivato' => ['Originario o Derivato','getArrayValues'],
 'dicitura_opera' => 'Dicitura opera'

]
],

[
'title' => 'Corrispettivo',
'content_opt_group' => [

 'tipologia_cessione' => ['Tipologia cessione','getArrayValues'],
 'importo_corrispettivo' => 'Importo corrispettivo',
 'indirizzo_fattura' => 'Indirizzo fatturazione',
 'modalita_pagamento' => 'Modalità di pagamento'

]
],

[
'title' => 'Legge applicabile e Foro competente',
'content_opt_group' => [

 'sede_adr' => 'Sede ADR',
 'sede_foro' => 'Sede foro',
 'data' => ['Data del contratto','format_date'],
 'luogo' => 'Luogo del contratto'

]
]

];

