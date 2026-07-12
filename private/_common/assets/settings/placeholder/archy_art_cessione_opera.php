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
'title' => 'Dati Acquirente',
'content_opt_group' => [

 'nominativo_acquirente' => 'Nominativo acquirente',
 'indirizzo_acquirente' => 'Indirizzo acquirente',
 'cap_acquirente' => 'Cap acquirente',
 'comune_acquirente' => 'Comune acquirente',
 'provincia_acquirente' => 'Provincia acquirente',
 'stato_acquirente' => 'Nazione acquirente',
 'cod_fisc_acquirente' => 'Codice Fiscale acquirente',
 'p_iva_acquirente' => 'Partiva IVA acquirente',
 'tipologia_documento_acquirente' => 'Tipologia documento acquirente',
 'numero_documento_acquirente' => 'Numero documento acquirente',
 'telefono_acquirente' => 'Telefono acquirente',
 'fax_acquirente' => 'Fax acquirente',
 'email_acquirente' => 'Email acquirente',
 'pec_acquirente' => 'PEC acquirente'

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

 'qualifica_cedente' => 'Titolo disposizione',
 'nome_opera' => ['Nome dell\'Opera','custom_opera_title','opera_id','titolo_opera']

]
],

[
'title' => 'Obblighi, dichiarazioni e responsabilità',
'content_opt_group' => [

 'vivente' => ['Vivente','getArrayValues'],
 'tipo_di_cessione' => ['Titolo cessione','getArrayValues'],
 'titolo_originarieta' => ['Titolo originarietà','getArrayValues'],
 'spese_a_carico' => ['Spese imballaggio','getArrayValues'],
 'nome_trasportatore' => 'Nome trasportatore',
 'referente_restauro' => 'Referente restauro',
 'nome_archivio_artista' => 'Nominativo archivio'

]
],

[
'title' => 'Corrispettivo',
'content_opt_group' => [

 'corrispettivo' => ['Corrispettivo','getArrayValues'],
 'importo_corrispettivo' => 'Importo corrispettivo',
 'indirizzo_fattura' => 'Indirizzo fattura',
 'modalita_pagamento_corrispettivo' => 'Modalità di pagamento'

]
],

[
'title' => 'Diritti di utilizzazione economica',
'content_opt_group' => [

 'tipo_di_cessione' => ['Titolo cessione','getArrayValues'],
 'durata_cessione' => ['Durata cessione','getArrayValues'],
 'diritti_di_utilizzazione' => ['Diritti di utilizzazione','getArrayValues'],
 'dettagli_riproduzione' => ['Dettagli riproduzione','format_editor'],
 'dicitura_opera' => 'Dicitura opera',
 'numero_copie_fornite' => 'Numero di copie fornite',
 'facolta_di_prestito' => ['Facoltà di prestito','getArrayValues'],
 'valore_assicurativo' => 'Valore assicurativo',
 'nome_assicurazione' => 'Nominativo assicurazione',
 'nome_trasportatore' => 'Nome trasportatore'

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
