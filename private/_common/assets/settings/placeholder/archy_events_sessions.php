<?php

$placeHolders = [
    '[titolo_sessione]' => array('value'=>($q->title ?? null),'label'=>'Titolo sessione'),
    '[start_date]' => array('value'=>$this->app->format_date(($q->start_date ?? null)),'label'=>'Data inizio'),
    '[end_date]' => array('value'=>$this->app->format_date(($q->end_date ?? null)),'label'=>'Data fine'),
    '[nominativo_relatore]' => array('value'=>$speaker[0]->speaker[0]->name,'label'=>'Nominativo relatore'),
    '[location]' => array('value'=>$location->name,'label'=>'Nome location')
];

