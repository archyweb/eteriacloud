<?php

/* parameters description
    
    registry -> Tutti i dati della tabella archy_registry compreso i dati della città
    service -> Tutti i dati della tabella archy_services
    service_subscription -> Tutti i dati della tabella archy_services_subscriptions
    service_membership -> Tutti i dati della tabella archy_services_memberships
 
*/

return [    
    'default_placeholders'=>['main_targets' => array('archy_registry')],
    'default_settings'=>['main_targets' => array('archy_registry','archy_memberships')],
    'archy_services'=>['main_targets' => array('archy_registry','archy_services','archy_services_subscriptions','archy_services_memberships')],
    'archy_events_sessions'=>['main_targets' => array('archy_registry','archy_memberships','archy_events_sessions')],
    'archy_events'=>['main_targets' => array('archy_events')],
    'archy_meeting_categories'=>['main_targets' => array('archy_meetings_book','archy_memberships')],
    
    'archy_art_certificates'=>['main_targets' => array('archy_art_certificates','archy_art_catalog'),'params' => array('rel_table' => 'archy_art_certificates','rel_field' => 'opera_id')],
    'archy_art_catalog'=>
        ['main_targets' => array('archy_art_catalog'),
        'add_targets' => array('special','archy_art_authors','archy_art_exhibitions','archy_art_publications'),
        
        ],
    'archy_art_cessione_immagine'=>['main_targets' => array('archy_art_cessione_immagine')],
    'archy_art_cessione_opera'=>['main_targets' => array('archy_art_cessione_opera')],
    'archy_art_contratto_valutazione'=>['main_targets' => array('archy_art_contratto_valutazione')],
    'archy_art_condition_report'=>['main_targets' => array('archy_art_condition_report','archy_art_catalog'),'params' => array('rel_table' => 'archy_art_condition_report','rel_field' => 'opera_id')],
    'archy_art_diritto_riproduzione_immagine'=>['main_targets' => array('archy_art_diritto_riproduzione_immagine')],
    'archy_art_donazione'=>['main_targets' => array('archy_art_donazione')],
    'archy_art_noleggio'=>['main_targets' => array('archy_art_noleggio')],
    'archy_art_prestito'=>['main_targets' => array('archy_art_prestito')],
    'archy_art_prestito_definitivo'=>['main_targets' => array('archy_art_prestito_definitivo')],
    'archy_art_prestito_promessa'=>['main_targets' => array('archy_art_prestito_promessa')],
    'archy_art_privacy'=>['main_targets' => array('archy_art_privacy')],
    'archy_messages'=>['main_targets' => array('archy_registry','archy_memberships')]
];