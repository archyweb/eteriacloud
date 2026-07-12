<?php

$configPlaceholders = [
    'Stato'        => ['Stato', 'field' => 'status'],  //$options=['1'=>'Associato','2'=>'In attesa di rinnovo','3'=>'Dimesso','4'=>'Cessato','5'=>'In attesa prima quota'];
    'Tessera'  =>['Numero tessera', 'field' => 'card'],
    'DataTessera' => ['Data di inizio', 'format_date', 'field' => 'membership_begin'],
    'ScadenzaTessera'  => ['Data di fine', 'format_date', 'field' => 'membership_end'],
    'Picture' => ['Immagine profilo', 'field' => 'picture'],
    'DataAdesione' => ['Data accettazione', 'format_date', 'field' => 'acceptance_date'],
    'DataCessazione' => ['Data cessazione', 'format_date', 'field' => 'resignation_date'],
    'MotivoCessazione' => ['Motivo cessazione', 'field' => 'resignation_reason']
];