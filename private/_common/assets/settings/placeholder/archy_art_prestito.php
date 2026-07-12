<?php

$configPlaceholders = [

[
'title' => 'Dati Prestatore',
'content_opt_group' => [

 'nominativo_archivio' => 'Nominativo prestatore',
 'indirizzo_archivio' => 'Indirizzo prestatore',
 'cap_archivio' => 'Cap prestatore',
 'comune_archivio' => 'Comune prestatore',
 'provincia_archivio' => 'Provincia prestatore',
 'stato_archivio' => 'Nazione prestatore',
 'cod_fisc_archivio' => 'Codice Fiscale prestatore',
 'p_iva_archivio' => 'Partiva IVA prestatore',
 'tipologia_documento_archivio' => 'Tipologia documento prestatore',
 'numero_documento_archivio' => 'Numero documento prestatore',
 'telefono_archivio' => 'Telefono prestatore',
 'fax_archivio' => 'Fax prestatore',
 'email_archivio' => 'Email prestatore',
 'pec_archivio' => 'PEC prestatore'

]
],

[
'title' => 'Dati Prestatario',
'content_opt_group' => [

 'nominativo_controparte' => 'Nominativo prestatario',
 'indirizzo_controparte' => 'Indirizzo prestatario',
 'cap_controparte' => 'Cap prestatario',
 'comune_controparte' => 'Comune prestatario',
 'provincia_controparte' => 'Provincia prestatario',
 'stato_controparte' => 'Nazione prestatario',
 'cod_fisc_controparte' => 'Codice Fiscale prestatario',
 'p_iva_controparte' => 'Partiva IVA prestatario',
 'tipologia_documento_controparte' => 'Tipologia documento prestatario',
 'numero_documento_controparte' => 'Numero documento prestatario',
 'telefono_controparte' => 'Telefono prestatario',
 'fax_controparte' => 'Fax prestatario',
 'email_controparte' => 'Email prestatario',
 'pec_controparte' => 'PEC prestatario'

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
 'nome_opera' => ['Titolo dell\'Opera','custom_opera_title','opera_id','titolo_opera'],
 'valore_opera' => 'Valore dell\'Opera',
 'termine_prestito' => ['Termine del prestito','format_date'],
 'scopo_utilizzo' => 'Scopo di utilizzo',
 'evento_prestito' => 'Nome dell\'evento',
 'date_evento' => ['Data dell\'evento','format_date'],
 'sede_evento' => 'Sede dell\'evento',
 'progetto_evento' => 'Tipo di progetto',
 'data_inizio' => ['Data inizio prestito','format_date'],
 'data_fine' => ['Data fine prestito','format_date'],
 'tacito_rinnovo' => ['Tacito rinnovo','getArrayValues'],
 'periodo_disdetta' => 'Periodo di disdetta',
 'giorni_preavviso' => 'Giorni di preavviso'

]
],

[
'title' => 'Obblighi, dichiarazioni e responsabilità',
'content_opt_group' => [

 'titolo_originarieta' => ['Titolo originarietà','getArrayValues'],
 'disponibilita_opera' => ['Disponibilita opera','getArrayValues'],
 'valore_assicurativo' => 'Valore assicurativo',
 'nome_assicurazione' => 'Nome assicurazione',
 'sede_riconsegna' => 'Sede riconsegna',
 'dettagli_materiali' => ['Dettagli materiali','format_editor'],
 'prestatatore_prestatario' => ['Prestatatore o Prestatario','getArrayValues'],
 'nome_trasportatore' => 'Nome del trasportatore'

]
],

[
'title' => 'Tutela dell’Opera',
'content_opt_group' => [

 'tipo_sorveglianza' => 'Tipo di sorveglianza',
 'esposizione_esterno' => ['Esposizione in esterna','getArrayValues']

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
'title' => 'Diritti di utilizzazione economica dell’Opera',
'content_opt_group' => [

 'tipo_di_cessione' => ['Tipo di cessione','getArrayValues'],
 'durata_cessione' => ['Durata cessione','getArrayValues'],
 'concessione_diritti' => 'Concessione diritti',
 'diritto_immagine' => 'Diritti d\'immagine',
 'diritti_utilizzazione' => ['Diritti utilizzazione','getArrayMultipleValues'],
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
 'luogo' => 'Luogo del contratto'

]
]

];
