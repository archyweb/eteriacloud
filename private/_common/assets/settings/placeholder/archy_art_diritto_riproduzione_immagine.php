<?php

$configPlaceholders = [

[
'title' => 'Dati Cedente',
'content_opt_group' => [

 'nominativo_cedente' => 'Nominativo cedente',
 'indirizzo_cedente' => 'Indirizzo cedente',
 'cap_cedente' => 'Cap cedente',
 'comune_cedente' => 'Comune cedente',
 'provincia_cedente' => 'Provincia cedente',
 'stato_cedente' => 'Nazione cedente',
 'cod_fisc_cedente' => 'Codice Fiscale cedente',
 'p_iva_cedente' => 'Partiva IVA cedente',
 'tipologia_documento_cedente' => 'Tipologia documento cedente',
 'numero_documento_cedente' => 'Numero documento cedente',
 'telefono_cedente' => 'Telefono cedente',
 'fax_cedente' => 'Fax cedente',
 'email_cedente' => 'Email cedente',
 'pec_cedente' => 'PEC cedente'

]
],

[
'title' => 'Dati Cessionario',
'content_opt_group' => [

 'nominativo_cessionario' => 'Nominativo cessionario',
 'indirizzo_cessionario' => 'Indirizzo cessionario',
 'cap_cessionario' => 'Cap cessionario',
 'comune_cessionario' => 'Comune cessionario',
 'provincia_cessionario' => 'Provincia cessionario',
 'stato_cessionario' => 'Nazione cessionario',
 'cod_fisc_cessionario' => 'Codice Fiscale cessionario',
 'p_iva_cessionario' => 'Partiva IVA cessionario',
 'tipologia_documento_cessionario' => 'Tipologia documento cessionario',
 'numero_documento_cessionario' => 'Numero documento cessionario',
 'telefono_cessionario' => 'Telefono cessionario',
 'fax_cessionario' => 'Fax cessionario',
 'email_cessionario' => 'Email cessionario',
 'pec_cessionario' => 'PEC cessionario'

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

 'titolo_opera' => 'Titolo dell\'Opera',
 'tipologia_evento' => ['Tipologia evento','format_editor'],
 'tipo_di_cessione' => ['Tipo di cessione','getArrayValues'],
 'durata_cessione' => ['Durata cessione','getArrayValues'],
 'durata_tempo' => 'Indicazione giorni cessione',
 'diritti_patrimoniali' => 'Diritti patrimoniali',
 'data_inizio' => ['Data inizio','format_date'],
 'data_fine' => ['Data fine','format_date'],
 'tacito_rinnovo' => ['Tacito rinnovo','getArrayValues'],
 'periodo_disdetta' => 'Periodo disdetta'

]
],

[
'title' => 'Obblighi, dichiarazioni e responsabilità',
'content_opt_group' => [

 'titolo_proprieta_cedente' => ['Titolo proprietà cedente','getArrayValues'],
 'originario_derivato' => ['Titolo originarieta','getArrayValues'],
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
