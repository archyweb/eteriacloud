<?php

$configPlaceholders = [
    'Azienda' => ['Nome azienda', 'field' => 'firm_name'],
    'name'  =>['Nome', 'field' => 'name'],
    'surname' => ['Cognome', 'format_date', 'field' => 'surname'],
    'RagioneSociale/Nominativo'  => ['Nome completo', 'field' => 'label'],
    'PartitaIVA' => ['Partita iva', 'field' => 'vat_id'],
    'CodiceFiscale' => ['Codice fiscale', 'field' => 'tax_code'],
    'zip' => [
        'Cap',
        'field' => 'zip',
        'join' => [
            'table' => 'archy_cities',
            'local_field' => 'city_id'
        ]
    ],
    'Indirizzo' => ['Indirizzo', 'field' => 'address'],
    'Comune' => [
        'Comune',
        'field' => 'city',
        'join' => [
            'table' => 'archy_cities',
            'local_field' => 'city_id'
        ]
    ],
    'Provincia' => [
        'Provincia',
        'field' => 'province_district',
        'join' => [
            'table' => 'archy_cities',
            'local_field' => 'city_id'
        ]
    ],
    'SiglaProvincia' => [
        'SiglaProvincia',
        'field' => 'province_district_code',
        'join' => [
            'table' => 'archy_cities',
            'local_field' => 'city_id'
        ]
    ],
    'Regione' => [
        'Regione',
        'field' => 'state_region',
        'join' => [
            'table' => 'archy_cities',
            'local_field' => 'city_id'
        ]
    ],
    'NazioneIT' => [
        'Stato',
        'field' => 'country_it',
        'join' => [
            [
                'table' => 'archy_cities',
                'local_field' => 'city_id'
            ],
            [
                'table' => 'archy_countries',
                'local_field' => 'country_id'
            ]
        ]
    ],
    'Telefono' => ['Telefono', 'field' => 'telephone'],
    'Fax' => ['Fax', 'field' => 'fax'],
    'Cellulare'  =>['Cellulare', 'field' => 'mobile'],
    'EMail' => ['EMail', 'field' => 'email'],
    'PEC'  => ['PEC',  'field' => 'certified_email'],
    'SitoWeb' => ['Sito internet', 'field' => 'website'],
    'DataNascita' => ['Data di nascita', 'format_date', 'field' => 'birth_date'],
    'LuogoNascita' => ['Luogo di nascita', 'field' => 'birth_place'],
    'Foto' => ['Immagine profilo', 'field' => 'main_picture']
];
