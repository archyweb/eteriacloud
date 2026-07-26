<?php

require_once __DIR__ . '/classes/ArchyEnv.php';

return [
    'core_update_web_token' => ArchyEnv::value('CORE_UPDATE_WEB_TOKEN', ''),
    'core_update_web_allow_live' => true,
    'core_update_manifest_url' => 'https://update.archyart.it/manifest.json',
    'core_update_customers_path' => 'private/_common/update/customers.csv',
    'login_email_otp' => false,

    /*
    |--------------------------------------------------------------------------
    | Estensione menu per singolo sottodominio
    |--------------------------------------------------------------------------
    |
    | Questo blocco va copiato nel config-local.php del sottodominio che deve
    | avere voci menu specifiche. Non modifica private/_common/config_menu.php.
    |
    */
    'menu_extend' => [
        'master' => [
            'text' => 'Master',
            'icon' => 'fas fa-server',
            'enabled' => true,
            'after' => 'admin',
            'items' => [
                [
                    'text' => 'Test aggiornamento DB',
                    'href' => '/srv/core-update/web-run.php?token={core_update_web_token}&manifest={core_update_manifest_url}&customers={core_update_customers_path}&dry_run=1&debug=1',
                    'id' => 'core_update_db_test',
                    'anchor_attributes' => 'class="custom" target="_blank"',
                    'enabled' => true
                ],
                [
                    'text' => 'Esegui aggiornamento DB',
                    'href' => '/srv/core-update/web-run.php?token={core_update_web_token}&manifest={core_update_manifest_url}&customers={core_update_customers_path}&dry_run=0&debug=1',
                    'id' => 'core_update_db_run',
                    'condition' => 1,
                    'anchor_attributes' => 'class="custom" target="_blank"',
                    'enabled' => true
                ],
                ['divider' => true],
                [
                    'text' => 'Test aggiornamento files',
                    'href' => '/srv/core-update/web-run.php?token={core_update_web_token}&manifest={core_update_manifest_url}&customers={core_update_customers_path}&mode=files&dry_run=1&debug=1',
                    'id' => 'core_update_files_test',
                    'anchor_attributes' => 'class="custom" target="_blank"',
                    'enabled' => false
                ],
                [
                    'text' => 'Esegui aggiornamento files',
                    'href' => '/srv/core-update/web-run.php?token={core_update_web_token}&manifest={core_update_manifest_url}&customers={core_update_customers_path}&mode=files&dry_run=0&debug=1',
                    'id' => 'core_update_files_run',
                    'condition' => 1,
                    'anchor_attributes' => 'class="custom" target="_blank"',
                    'enabled' => false
                ],
                ['divider' => true],
                [
                    'text' => 'Ticket supporto',
                    'href' => 'archy/app.php?view=tickets',
                    'id' => 'master_tickets',
                    'enabled' => true
                ]
            ]
        ],
        'admin' => [
            'remove_items' => ['archy_support'],
            'items' => [
                ['divider' => true],
                [
                    'text' => 'Voce admin locale',
                    'href' => 'archy/app.php?view=dashboard',
                    'id' => 'local_admin_item',
                    'enabled' => true
                ]
            ]
        ]
    ]
];
