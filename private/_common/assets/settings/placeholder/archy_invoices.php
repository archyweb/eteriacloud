<?php

$placeHolders = [
    '[NumeroFattura]' => array('value'=>($q->number ?? null),'label'=>'Numero'),
    '[DataFattura]' => array('value'=>$this->app->format_date(($q->date ?? null)),'label'=>'Data'),
    '[ImportoFattura]' => array('value'=>($q->total ?? null),'label'=>'Importo'),
    '[ScadenzaFattura]' => array('value'=>($q->collocazione_geografica ?? null),'label'=>'Scadenza'),
    '[CorpoFattura]' => array('value'=>($q->precedenti_archiviazioni ?? null),'label'=>'Corpo (tabella)'), // da compilare
    '[NoteFattura]' => array('value'=>($q->notes ?? null),'label'=>'Note')
];