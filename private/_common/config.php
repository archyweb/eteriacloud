<?php
return [
    'maintenance' => false,
    'acube' => [
        'sandbox' => [
            'url' => 'https://api-sandbox.acubeapi.com',
            'email' => 'andrea@movieweb.it',
            'password' => 'cpNUBe5ei2SfhUO7'
        ],
        'production' => [
            'url' => 'https://api.acubeapi.com',
            'email' => 'andrea@movieweb.it',
            'password' => 'Hyb%6gtv&cIo'
        ]
    ],
    'customer_care' => [
        'tech' => [
            'mail' => 'tech@archymedia.com',
            'name' => 'Assistenza tecnica Eteria',
            'bcc' => ''
        ],
        'admin' => [
            'mail' => 'helpdesk@archymedia.com',
            'name' => 'Assistenza amministrativa Eteria',
            'bcc' => ''
        ],
        'phone' => [
            'number' => '', // null string to disable
            'timetables' => '<p class="timetables">Dal Lunedì al Venerdì<br>Dalle ore 09:00 alle ore 13:00 e dalle ore 15:00 alle ore 19:00</p>'
        ]
    ],
    'dashboard' => [
        'calendar' => true,
        'notify' => false,
        'stats' => true
    ]
    
    /*
    'single_record' => [
        'archy_firms' => '
			<h3>Registri sezionali multipli disabilitati</h3>
			<p>Con il piano attuale è possibile utilizzare un unico registro sezionale.</p>
			<p>Contattaci via telefono o tramite ticket di assistenza per aggiungere altri registri.</p>
			<button type="button" class="btn btn-primary customer_service">Servizio Clienti</button>
			<button type="button" class="btn btn-default" onClick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left"></span> Indietro</button>
		'
    ]*/
];
