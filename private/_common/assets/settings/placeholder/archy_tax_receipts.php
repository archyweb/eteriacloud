<?php

$placeHolders = [
    '[NumeroRicevuta]' => array('value'=>($q->number ?? null),'label'=>'Numero'),
    '[DataRicevuta]' => array('value'=>$this->app->format_date(($q->date ?? null)),'label'=>'Data'),
    '[ImportoRicevuta]' => array('value'=>($q->total ?? null),'label'=>'Importo'),
    '[ScadenzaRicevuta]' => array('value'=>($q->precedenti_archiviazioni ?? null),'label'=>'Scadenza'),
    '[CorpoRicevuta]' => array('value'=>($q->precedenti_archiviazioni ?? null),'label'=>'Corpo (tabella)'), // da compilare
    '[NoteRicevuta]' => array('value'=>($q->notes ?? null),'label'=>'Note')
];