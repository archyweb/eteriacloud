<?php

$placeHolders = [
    '[InizioServizio]' => array('value'=>$this->app->format_date(($q->membership_begin ?? null)),'label'=>'Inizio servizio'),
    '[ScadenzaServizio]' => array('value'=>$this->app->format_date(($q->membership_end ?? null)),'label'=>'Scadenza servizio')
];

