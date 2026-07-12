<?php

$placeHolders = [
    '[NomeServizio]' => array('value'=>($q->description ?? null),'label'=>'Titolo servizio'),
    '[PrezzoServizio]' => array('value'=>($q->amount ?? null),'label'=>'Totale costo servizio'),
    '[CausaleServizio]' => array('value'=>($q->accounting_reason ?? null),'label'=>'Causale servizio')
];

