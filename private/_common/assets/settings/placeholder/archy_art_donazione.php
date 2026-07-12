<?php

$configPlaceholders = [

[
'title' => 'Dati Donante',
'content_opt_group' => [

 'nominativo_donante' => 'Nominativo donante',
 'indirizzo_donante' => 'Indirizzo donante',
 'cap_donante' => 'Cap donante',
 'comune_donante' => 'Comune donante',
 'provincia_donante' => 'Provincia donante',
 'stato_donante' => 'Nazione donante',
 'cod_fisc_donante' => 'Codice Fiscale donante',
 'p_iva_donante' => 'Partiva IVA donante',
 'tipologia_documento_donante' => 'Tipologia documento donante',
 'numero_documento_donante' => 'Numero documento donante',
 'telefono_donante' => 'Telefono donante',
 'fax_donante' => 'Fax donante',
 'email_donante' => 'Email donante',
 'pec_donante' => 'PEC donante',
 'archivio_artista' => 'Archivio artista'

]
],

[
'title' => 'Dati Donatario',
'content_opt_group' => [

 'nominativo_donatario' => 'Nominativo donatario',
 'indirizzo_donatario' => 'Indirizzo donatario',
 'cap_donatario' => 'Cap donatario',
 'comune_donatario' => 'Comune donatario',
 'provincia_donatario' => 'Provincia donatario',
 'stato_donatario' => 'Nazione donatario',
 'cod_fisc_donatario' => 'Codice Fiscale donatario',
 'p_iva_donatario' => 'Partiva IVA donatario',
 'tipologia_documento_donatario' => 'Tipologia documento donatario',
 'numero_documento_donatario' => 'Numero documento donatario',
 'telefono_donatario' => 'Telefono donatario',
 'fax_donatario' => 'Fax donatario',
 'email_donatario' => 'Email donatario',
 'pec_donatario' => 'PEC donatario'

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

 'titolo_disposizione' => 'Titolo disposizione',
 'nome_opera' => ['Titolo Opera','custom_opera_title','opera_id','titolo_opera'],
 'privato_pubblico' => ['Privato/Pubblico','getArrayValues']

]
],

[
'title' => 'Obblighi, dichiarazioni e responsabilità',
'content_opt_group' => [

 'vivente' => ['Archivio o Artista Vivente','getArrayValues','vivente_donazione'],
 'nominativo_autore' => 'Nominativo autore',
 'titolarita' => ['Titolarità','getArrayValues'],
 'titolo_proprieta_donante' => 'Titolo proprietà donante',
 'originario_derivato' => ['Originario/Derivato','getArrayValues'],
 'valore_opera' => 'Valore opera',
 'referente_restauro' => 'Referente restauro',
 'nome_archivio_artista' => 'Nome archivio artista',
 'spese_imballaggio' => ['Spese imballaggio','getArrayValues'],
 'nome_trasportatore' => 'Nome trasportatore'

]
],

[
'title' => 'Diritti di utilizzazione economica',
'content_opt_group' => [

 'tipo_di_cessione' => ['Tipo di cessione','getArrayValues'],
 'durata_cessione' => ['Durata cessione','getArrayValues'],
 'diritti_opera' => ['Diritti opera','getArrayMultipleValues'],
 'dettagli_riproduzione' => ['Dettagli riproduzione','format_editor'],
 'dicitura_opera' => 'Dicitura opera',
 'numero_copie' => 'Numero di copie',
 'facolta_di_prestito' => ['Facoltà di prestito','getArrayValues'],
 'valore_assicurativo' => 'Valore assicurativo',
 'nome_assicurazione' => 'Nominativo assicurazione'

]
],

[
'title' => 'Clausole finali',
'content_opt_group' => [

 'sede_adr' => 'Sede ADR',
 'sede_foro' => 'Sede foro',
 'data' => ['Data del contratto','format_date'],
 'luogo' => 'Luogo del contratto'

]
]

];

