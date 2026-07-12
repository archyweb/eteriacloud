<?php

$placeHolders = [
    '[Logo]' => array('value'=>($q->number ?? null),'label'=>'Logo'),
    '[IndirizzoFatturazione Emittente]' => array('value'=>$this->app->format_date(($q->date ?? null)),'label'=>'Indirizzo (completo) di Fatturazione'),
    '[RagioneSociale Emittente]' => array('value'=>($q->total ?? null),'label'=>'Ragione Sociale'),
    '[PartitaIVA Emittente]' => array('value'=>($q->collocazione_geografica ?? null),'label'=>'Partita IVA'),
    '[CodiceFiscale Emittente]' => array('value'=>($q->precedenti_archiviazioni ?? null),'label'=>'Codive Fiscale'), // da compilare
    '[Indirizzo Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Indirizzo'),
    '[CAP Emittente]' => array('value'=>($q->notes ?? null),'label'=>'CAP'),
    '[Comune Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Comune'),
    '[Provincia/Distretto Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Provincia'),
    '[SiglaProvincia/Distretto Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Sigla provincia'),
    '[Regione/Stato Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Regione'),
    '[Nazione Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Nazione'),
    '[Telefono Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Telefono'),
    '[Fax Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Fax'),
    '[Cellulare Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Cellulare'),
    '[EMail Emittente]' => array('value'=>($q->notes ?? null),'label'=>'E-mail'),
    '[PEC Emittente]' => array('value'=>($q->notes ?? null),'label'=>'PEC'),
    '[SitoWeb Emittente]' => array('value'=>($q->notes ?? null),'label'=>'Sito web')
    
];