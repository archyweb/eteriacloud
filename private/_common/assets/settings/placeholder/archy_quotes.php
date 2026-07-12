<?php

$placeHolders = [
    '[NumeroPreventivo]' => array('value'=>($q->number ?? null),'label'=>'Numero'),
    '[DataPreventivo]' => array('value'=>$this->app->format_date(($q->date ?? null)),'label'=>'Data'),
    '[ImportoPreventivo]' => array('value'=>($q->total ?? null),'label'=>'Importo'),
    '[TabellaPreventivo]' => array('value'=>($q->precedenti_archiviazioni ?? null),'label'=>'Tabella'), // da compilare
    '[CorpoPreventivo]' => array('value'=>($q->notes ?? null),'label'=>'Corpo')
];