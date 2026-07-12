<?php

$placeHolders = [
    '[NumeroNota]' => array('value'=>($q->number ?? null),'label'=>'Numero'),
    '[DataNota]' => array('value'=>$this->app->format_date(($q->date ?? null)),'label'=>'Data'),
    '[ImportoNota]' => array('value'=>($q->total ?? null),'label'=>'Importo'),
    '[CorpoNota]' => array('value'=>($q->precedenti_archiviazioni ?? null),'label'=>'Corpo (tabella)'), // da compilare
    '[Note]' => array('value'=>($q->notes ?? null),'label'=>'Note')
];