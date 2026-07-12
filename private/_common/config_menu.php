<?php
return [    
    'menu' => [
        'admin' => [
            'text' => 'Admin',
            'icon' => 'fas fa-chart-pie',
            'enabled' => true,
            'items' => [
                [
                    'text' => 'Utenti',
                    'href' => 'archy/app.php?intent=archy_users&view=grid',
                    'id' => 'archy_users',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Campi aggiuntivi',
                    'href' => 'archy/app.php?intent=additional_fields&view=grid',
                    'id' => 'additional_fields',
                    'enabled' => true
                ],   
                ['divider' => true],
                [
                    'text' => 'Messaggi',
                    'href' => 'archy/app.php?intent=archy_messages&view=grid',
                    'id' => 'archy_messages',
                    'enabled' => true
                ],
                [
                    'text' => 'Coda SMTP',
                    'href' => 'archy/app.php?intent=archy_queue&view=grid',
                    'id' => 'archy_queue',
                    'enabled' => true
                ],
                [
                    'text' => 'Impostazioni SMTP',
                    'href' => 'archy/app.php?view=settings&domain=global',
                    'id' => 'global_settings',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Esegui CRON DAILY',
                    'href' => 'https://archymedia.archymedia.com/assets/cronjobs/daily.php',
                    'id' => 'global_settings',                    
                    'condition' => 2, // super admin
                    'anchor_attributes'=>'class="custom" target="_blank"',
                    'enabled' => true
                ],
                [
                    'text' => 'Esegui CRON MAILER',
                    'href' => 'https://archymedia.archymedia.com/assets/cronjobs/mailer.php',
                    'id' => 'global_settings',
                    'condition' => 2, // super admin
                    'anchor_attributes'=>'class="custom" target="_blank"',
                    'enabled' => true
                ],     
                ['divider' => true],
                [
                    'text' => 'Comuni',
                    'href' => 'archy/app.php?intent=archy_cities&view=grid',
                    'id' => 'archy_cities',
                    'enabled' => true
                ],
                [
                    'text' => 'Nazioni',
                    'href' => 'archy/app.php?intent=archy_countries&view=grid',
                    'id' => 'archy_countries',
                    'condition' => 2, // super admin
                    'enabled' => true
                ]                    
            ]
        ],
        'association' => [
            'text' => 'Associazione',
            'icon' => 'fas fa-chalkboard-teacher',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Impostazioni Associazione',
                    'href' => 'archy/app.php?view=settings&domain=association_1',
                    'id' => 'settings_association_1',
                    'enabled' => true
                ],
                [
                    'text' => 'Impostazioni Associati',
                    'href' => 'archy/app.php?view=settings&domain=association_2',
                    'id' => 'settings_association_2',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Candidati associati',
                    'href' => 'archy/app.php?intent=archy_memberships_examinee&view=grid',
                    'id' => 'archy_memberships_examinee',
                    'enabled' => true
                ],
                [
                    'text' => 'Associati',
                    'href' => 'archy/app.php?intent=archy_memberships&view=grid',
                    'id' => 'archy_memberships',
                    'enabled' => true
                ],
                [
                    'text' => 'Quote associative',
                    'href' => 'archy/app.php?intent=archy_subscriptions&view=grid',
                    'id' => 'archy_subscriptions',
                    'enabled' => true
                ],
                [
                    'text' => 'Libro Soci',
                    'href' => 'archy/app.php?intent=archy_members_history&view=grid',
                    'id' => 'archy_members_history',
                    'enabled' => true
                ],
                [
                    'text' => 'Stampa tessere',
                    'href' => 'archy/app.php?view=card',
                    'id' => 'archy_card',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Board',
                    'href' => 'archy/app.php?intent=archy_board&view=grid',
                    'id' => 'archy_board',
                    'enabled' => true
                ],
                [
                    'text' => 'Cariche associative',
                    'href' => 'archy/app.php?intent=archy_bodies&view=grid',
                    'id' => 'archy_bodies',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Sedi distaccate',
                    'href' => 'archy/app.php?intent=archy_branches&view=grid',
                    'id' => 'archy_branches',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Libro Adunanze',
                    'href' => 'archy/app.php?intent=archy_meetings_book&view=grid',
                    'id' => 'archy_meetings_book',
                    'enabled' => true
                ],
                [
                    'text' => 'Categorie Adunanza',
                    'href' => 'archy/app.php?intent=archy_meeting_categories&view=grid',
                    'id' => 'archy_meeting_categories',
                    'enabled' => true
                ]
            ]
        ],        
        'services' => [
            'text' => 'Servizi / EPS',
            'icon' => 'fas fa-truck-loading',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Servizi / EPS',
                    'href' => 'archy/app.php?intent=archy_services&view=grid',
                    'id' => 'archy_services',
                    'enabled' => true
                ],
                [
                    'text' => 'Abbonamenti ai Servizi / EPS',
                    'href' => 'archy/app.php?intent=archy_services_memberships&view=grid',
                    'id' => 'archy_services_memberships',
                    'enabled' => true
                ],
                [
                    'text' => 'Quote per i Servizi / EPS',
                    'href' => 'archy/app.php?intent=archy_services_subscriptions&view=grid',
                    'id' => 'archy_services_subscriptions',
                    'enabled' => true
                ],
            ]
        ],
        'anagrafiche' => [
            'text' => 'Anagrafiche',
            'icon' => 'fas fa-user',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Anagrafiche',
                    'href' => 'archy/app.php?intent=archy_registry&view=grid',
                    'id' => 'archy_registry',
                    'enabled' => true
                ],
                [
                    'text' => 'Categorie',
                    'href' => 'archy/app.php?intent=archy_registry_categories&view=grid',
                    'id' => 'archy_registry_categories',
                    'enabled' => true
                ]
            ]
        ],
        'accounting' => [
            'text' => 'Contabilità',
            'icon' => 'fas fa-chart-line',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Impostazioni Contabilità',
                    'href' => 'archy/app.php?intent=archy_firms&view=grid',
                    'id' => 'archy_firms',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Anagrafiche<key> (CTRL+SHIFT+A)</key>',
                    'href' => 'archy/app.php?intent=archy_registry&view=grid',
                    'id' => 'archy_registry',
                    'enabled' => true
                ],
                [
                    'text' => 'Categorie<key> (CTRL+SHIFT+C)</key>',
                    'href' => 'archy/app.php?intent=archy_registry_categories&view=grid',
                    'id' => 'archy_registry_categories',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Registrazioni contabili',
                    'href' => 'archy/app.php?intent=archy_petty_cash_book&view=grid',
                    'id' => 'archy_petty_cash_book',
                    'enabled' => true
                ],
                [
                    'text' => 'Strumenti di pagamento',
                    'href' => 'archy/app.php?intent=archy_petty_cash_types&view=grid',
                    'id' => 'archy_petty_cash_types',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Bilancio di Cassa',
                    'href' => 'archy/app.php?intent=archy_cash_budget&view=grid',
                    'id' => 'archy_cash_budget',
                    'enabled' => false
                ],
                [
                    'text' => 'Fatture',
                    'href' => 'archy/app.php?intent=archy_invoices&view=grid',
                    'id' => 'archy_invoices',
                    'enabled' => true,
                    //'filter' => 'accounting_piva'
                ],
                [
                    'text' => 'Note di Credito Emesse',
                    'href' => 'archy/app.php?intent=archy_credit_notes&view=grid',
                    'id' => 'archy_credit_notes',
                    'enabled' => true,
                   // 'filter' => 'accounting_piva'
                ],
                [
                    'text' => 'Note di Credito Ricevute',
                    'href' => 'archy/app.php?intent=archy_credit_notes_from_sdi&view=grid',
                    'id' => 'archy_credit_notes_from_sdi',
                    'enabled' => true,
                   // 'filter' => 'accounting_piva'
                ],
                [
                    'text' => 'Ricevute',
                    'href' => 'archy/app.php?intent=archy_tax_receipts&view=grid',
                    'id' => 'archy_tax_receipts',
                    'enabled' => true
                ],
                [
                    'text' => 'Storno ricevute',
                    'href' => 'archy/app.php?intent=archy_passive_receipts&view=grid',
                    'id' => 'archy_passive_receipts',
                    'enabled' => true
                ],
                [
                    'text' => 'Modalità di pagamento',
                    'href' => 'archy/app.php?intent=archy_payment_methods&view=grid',
                    'id' => 'archy_payment_methods',
                    'enabled' => true
                ],
                [
                    'text' => 'Banche',
                    'href' => 'archy/app.php?intent=archy_banks&view=grid',
                    'id' => 'archy_banks',
                    'enabled' => true
                ],
                [
                    'text' => 'Aliquote IVA',
                    'href' => 'archy/app.php?intent=archy_tax_rates&view=grid',
                    'id' => 'archy_tax_rates',
                    'enabled' => true,
                    'filter' => 'accounting_piva'
                ],
                ['divider' => true],
                [
                    'text' => 'Documenti di acquisto',
                    'href' => 'archy/app.php?intent=archy_payables&view=grid',
                    'id' => 'archy_payables',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Tipologia di costi',
                    'href' => 'archy/app.php?intent=archy_cost_type&view=grid',
                    'id' => 'archy_cost_type',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Sezioni contabili',
                    'href' => 'archy/app.php?intent=archy_cost_matrice&view=grid',
                    'id' => 'archy_cost_matrice',
                    'enabled' => true
                ],
                [
                    'text' => 'Categorie contabili',
                    'href' => 'archy/app.php?intent=archy_cost_centres_categories&view=grid',
                    'id' => 'archy_cost_centres_categories',
                    'enabled' => true
                ],
                [
                    'text' => 'Voci contabili',
                    'href' => 'archy/app.php?intent=archy_cost_centres&view=grid',
                    'id' => 'archy_cost_centres',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Statistiche fatturato',
                    'href' => 'archy/app.php?view=invoices-stats',
                    'id' => 'invoices-stats',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Registro beni ammortizzabili',
                    'href' => 'archy/app.php?intent=archy_depreciable_assets&view=grid',
                    'id' => 'archy_depreciable_assets',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Preventivi',
                    'href' => 'archy/app.php?intent=archy_quotes&view=grid',
                    'id' => 'archy_quotes',
                    'enabled' => true
                ]
            ]
        ],
        'events' => [
            'text' => 'Attività',
            'icon' => 'fas fa-bezier-curve',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Eventi, Corsi, Seminari',
                    'href' => 'archy/app.php?intent=archy_events&view=grid',
                    'id' => 'archy_events',
                    'enabled' => true
                ],
                [
                    'text' => 'Iscrizioni',
                    'href' => 'archy/app.php?intent=archy_events_subscriptions&view=grid',
                    'id' => 'archy_events_subscriptions',
                    'enabled' => true
                ],
                [
                    'text' => 'Sessioni e Iscritti',
                    'href' => 'archy/app.php?intent=archy_events_sessions&view=grid',
                    'id' => 'archy_events_sessions',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Categorie',
                    'href' => 'archy/app.php?intent=archy_events_categories&view=grid',
                    'id' => 'archy_events_categories',
                    'enabled' => true
                ],
                [
                    'text' => 'Locations',
                    'href' => 'archy/app.php?intent=archy_events_locations&view=grid',
                    'id' => 'archy_events_locations',
                    'enabled' => true
                ],
                [
                    'text' => 'Relatori',
                    'href' => 'archy/app.php?intent=archy_events_speakers&view=grid',
                    'id' => 'archy_events_speakers',
                    'enabled' => true
                ],
                [
                    'text' => 'Extra',
                    'href' => 'archy/app.php?intent=archy_events_extras&view=grid',
                    'id' => 'archy_events_extras',
                    'enabled' => true
                ],
                [
                    'text' => 'Interventi',
                    'href' => 'archy/app.php?intent=archy_events_speeches&view=grid',
                    'id' => 'archy_events_speeches',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Rating (Qualifiche/Requisiti)',
                    'href' => 'archy/app.php?intent=archy_ratings&view=grid',
                    'id' => 'archy_ratings',
                    'enabled' => true
                ],
                [
                    'text' => 'Abbinamento anagrafiche/rating',
                    'href' => 'archy/app.php?intent=archy_rating_relations&view=grid',
                    'id' => 'archy_rating_relations',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Sconti',
                    'href' => 'archy/app.php?intent=archy_discounts&view=grid',
                    'id' => 'archy_discounts',
                    'enabled' => true
                ],
                [
                    'text' => 'Abbinamento rating/sconti',
                    'href' => 'archy/app.php?intent=archy_discount_relations&view=grid',
                    'id' => 'archy_discount_relations',
                    'enabled' => true
                ],
                [
                    'text' => 'Genera attestati',
                    'href' => 'archy/app.php?view=events_certificates',
                    'id' => 'archy_events_certificate',
                    'enabled' => true
                ],
            ]
        ],        
        'comitato' => [
            'text' => 'Comitato',
            'icon' => 'fas fa-globe',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Impostazioni Comitato',
                    'href' => 'archy/app.php?view=settings&domain=committee',
                    'id' => 'settings_committee',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Diario attività',
                    'href' => 'archy/app.php?intent=archy_art_diary&view=grid',
                    'id' => 'archy_art_diary',
                    'enabled' => true
                ],
                [
                    'text' => 'Categorie Diario',
                    'href' => 'archy/app.php?intent=archy_art_diary_category&view=grid',
                    'id' => 'archy_art_diary_category',
                    'enabled' => true
                ]
            ]
        ],
        'arts2' => [
            'text' => 'Catalogo Archivio',
            'icon' => 'fas fa-portrait',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Catalogo archivio opere e richieste',
                    'href' => 'archy/app.php?intent=archy_art_catalog&view=grid',
                    'id' => 'archy_art_catalog',
                    'enabled' => true
                ],
                [
                    'text' => 'Modello scheda catalogo',
                    'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_catalog',
                    'id' => 'archy_art_catalog_model',
                    'enabled' => true
                ],
                [
                    'text' => 'Impostazioni scheda catalogo',
                    'href' => 'archy/app.php?intent=archy_art_settings&view=grid',
                    'id' => 'archy_art_settings',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Serie di opere',
                    'href' => 'archy/app.php?intent=archy_art_series&view=grid',
                    'id' => 'archy_art_series',
                    'enabled' => true
                ],
                [
                    'text' => 'Collezioni',
                    'href' => 'archy/app.php?intent=archy_art_collections&view=grid',
                    'id' => 'archy_art_collections',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Esposizioni',
                    'href' => 'archy/app.php?intent=archy_art_exhibitions&view=grid',
                    'id' => 'archy_art_exhibitions',
                    'enabled' => true
                ],
                [
                    'text' => 'Pubblicazioni',
                    'href' => 'archy/app.php?intent=archy_art_publications&view=grid',
                    'id' => 'archy_art_publications',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Autori',
                    'href' => 'archy/app.php?intent=archy_art_authors&view=grid',
                    'id' => 'archy_art_authors',
                    'enabled' => true
                ],
				 ['divider' => true],
                [
                    'text' => 'Tag',
                    'href' => 'archy/app.php?intent=archy_tag&view=grid',
                    'id' => 'archy_tag',
                    'enabled' => true
                ],
            ]
        ],
        'arts' => [
            'text' => 'Comitato per le autentiche',
            'icon' => 'fas fa-people-arrows',
            'enabled' => true,
            'condition' => '',
            'items' => [
                [
                    'text' => 'Certificati',
                    'href' => 'archy/app.php?intent=archy_art_certificates&view=grid',
                    'id' => 'archy_art_certificates',
                    'enabled' => true
                ],
                [
                    'text' => 'Modelli certificato',
                    'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_certificates',
                    'id' => 'archy_art_certificates_models',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Condition report',
                    'href' => 'archy/app.php?intent=archy_art_condition_report&view=grid',
                    'id' => 'archy_art_condition_report',
                    'enabled' => true
                ],
                [
                    'text'=>'Modelli condition report',
                    'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_condition_report',
                    'id'=>'archy_art_condition_report_model',
                    'enabled'=>true
                ],
                [
                    'text' => 'Verbali Comitato',
                    'enabled' => false,
                    'id' => 'verbali',
                    'items' => [
                        [
                            'text' => 'Generazione verbali',
                            'href' => 'archy/app.php?intent=archy_verbali_comitato&view=grid',
                            'id' => 'archy_verbali_comitato',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Categorie verbali',
                            'href' => 'archy/app.php?intent=archy_verbali_comitato_categorie&view=grid',
                            'id' => 'archy_verbali_comitato_categorie',
                            'enabled' => true
                        ]
                    ]
                ]
            ]
        ],        
        'contract' => [
            'text' => 'Contrattualistica',
            'icon' => 'fas fa-file-contract',
            'enabled' => true,
            'condition' => '',
            'position' => 'dropdown-menu-end',
            'items' => [
                [
                    'text' => 'Contratti',
                    'enabled' => true,
                    'id' => 'contratti',
                    'items' => [
                        [
                            'text' => 'Noleggio',
                            'href' => 'archy/app.php?intent=archy_art_noleggio&view=grid',
                            'id' => 'archy_art_noleggio',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Donazione',
                            'href' => 'archy/app.php?intent=archy_art_donazione&view=grid',
                            'id' => 'archy_art_donazione',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Cessione diritti d\'immagine',
                            'href' => 'archy/app.php?intent=archy_art_cessione_immagine&view=grid',
                            'id' => 'archy_art_cessione_immagine',
                            //'anchor_attributes'=>'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Cessione diritti patrimoniali',
                            'href' => 'archy/app.php?intent=archy_art_diritto_riproduzione_immagine&view=grid',
                            'id' => 'archy_art_diritto_riproduzione_immagine',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Cessione opera',
                            'href' => 'archy/app.php?intent=archy_art_cessione_opera&view=grid',
                            'id' => 'archy_art_cessione_opera',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Valutazione opera',
                            'href' => 'archy/app.php?intent=archy_art_contratto_valutazione&view=grid',
                            'id' => 'archy_art_contratto_valutazione',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Prestito',
                            'href' => 'archy/app.php?intent=archy_art_prestito&view=grid',
                            'id' => 'archy_art_prestito',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Promessa di prestito',
                            'href' => 'archy/app.php?intent=archy_art_prestito_promessa&view=grid',
                            'id' => 'archy_art_prestito_promessa',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Prestito definitivo',
                            'href' => 'archy/app.php?intent=archy_art_prestito_definitivo&view=grid',
                            'id' => 'archy_art_prestito_definitivo',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Moduli Privacy',
                            'href' => 'archy/app.php?intent=archy_art_privacy&view=grid',
                            'id' => 'archy_art_privacy',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ]
                    ]
                ],
                [
                    'text' => 'Modelli',
                    'enabled' => true,
                    'id' => 'modelli',
                    'items' => [
                         [
                            'text' => 'Modelli noleggio',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_noleggio',
                            'id' => 'archy_art_noleggio_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli donazione',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_donazione',
                            'id' => 'archy_art_donazione_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli cessione immagine',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_cessione_immagine',
                            'id' => 'archy_art_cessione_immagine_model',
                            //'anchor_attributes'=>'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli diritti patrimoniali',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_diritto_riproduzione_immagine',
                            'id' => 'archy_art_diritti_riproduzione_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli cessione opera',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_cessione_opera',
                            'id' => 'archy_art_cessione_opera_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli valutazione opera',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_contratto_valutazione',
                            'id' => 'archy_art_contratto_valutazione_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli prestito',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_prestito',
                            'id' => 'archy_art_prestito_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli promessa prestito',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_prestito_promessa',
                            'id' => 'archy_art_prestito_promessa_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli prestito definitivo',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_prestito_definitivo',
                            'id' => 'archy_art_prestito_definitivo_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ],
                        [
                            'text' => 'Modelli privacy',
                            'href' => 'archy/app.php?intent=archy_models&view=grid&intent_filter=model_intent|archy_art_privacy',
                            'id' => 'archy_art_privacy_model',
                            //'anchor_attributes' => 'class="custom" target="_parent"',
                            'enabled' => true
                        ]
                    ]
                ]
            ]
        ]
    ]
];
