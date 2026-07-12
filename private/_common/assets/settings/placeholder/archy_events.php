<?php

$placeHolders = [
    '[titolo_evento]' => array('value'=>($q->title ?? null),'label'=>'Titolo evento'),
    '[descrizione_evento]' => array('value'=>($q->description ?? null),'label'=>'Descrizione evento'),
    '[total_credits]' => array('value'=>($q->total_credits ?? null),'label'=>'Crediti totali'),
    '[minimum_credits]' => array('value'=>($q->minimum_credits ?? null),'label'=>'Crediti minimi')
];

