<?php

$placeHolders = [
    '[NomePresidente]' => array('value'=>($q->president ?? null),'label'=>'Presidente'),
	'[NomeSegretario]' => array('value'=>($q->secretary ?? null),'label'=>'Segretario'),
	'[DataAdunanza]' => array('value'=>($q->date_time ?? null),'label'=>'Data'),
	'[OrarioAdunanza]' => array('value'=>($q->date_time ?? null),'label'=>'Orario'),
	'[LuogoAdunanza]' => array('value'=>($q->president ?? null),'label'=>'place'),
	'[ElencoCandidatiAmmessi]' => array('value'=>($q->president ?? null),'label'=>'ElencoCandidatiAmmessi'),
	'[ElencoCandidatiNonAmmessi]' => array('value'=>($q->president ?? null),'label'=>'ElencoCandidatiNonAmmessi'),
	'[ElencoAssociatiCessati]' => array('value'=>($q->president ?? null),'label'=>'ElencoAssociatiCessati'),
	'[ElencoAssociatiDecaduti]' => array('value'=>($q->president ?? null),'label'=>'ElencoAssociatiDecaduti'),
];
