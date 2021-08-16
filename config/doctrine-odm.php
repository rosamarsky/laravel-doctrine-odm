<?php

use Doctrine\ODM\MongoDB\DocumentManager;

return [
    'manager' => [
        'proxy_dir' => storage_path('odm/proxies'),
        'proxy_namespace' => 'OdmProxies',
        'hydrator_dir' => storage_path('odm/hydrators'),
        'hydrator_namespace' => 'Hydrators',
        'default_db' => env('MONGO_DB', 'default'),
        'metadata_dir' => app_path(),
    ],

    'database' => [
        'mongodb' => [
            'dsn' => sprintf('mongodb://%s:%d',
                env('MONGO_HOST', '127.0.0.1'),
                env('MONGO_PORT', '27017')
            ),

            'auth' => [
                'database' => env('MONGO_DB', 'default'),
                'username' => env('MONGO_USER', 'admin'),
                'password' => env('MONGO_PASS', ''),
            ],

            'options' => [
                'typeMap' => DocumentManager::CLIENT_TYPEMAP,
            ],
        ],
    ],
];
