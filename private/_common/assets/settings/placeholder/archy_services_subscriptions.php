<?php

$placeHolders = [
    '[DataVersamentoServizio]' => array('value'=>$this->app->format_date(($q->date ?? null)),'label'=>'Data di iscrizione'),
    '[Prezzo]' => array('value'=>($q->amount ?? null),'label'=>'Totale iscrizione')
];

