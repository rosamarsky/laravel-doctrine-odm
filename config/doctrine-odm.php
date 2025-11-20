<?php
declare(strict_types=1);

use Doctrine\ODM\MongoDB\DocumentManager;

return [
    'manager' => [
        'driver' => 'attributes', // attributes | xml
        'metadata_dir' => app_path(),

        'proxy_dir' => storage_path('odm/proxies'),
        'proxy_namespace' => 'OdmProxies',
        'auto_generate_proxy_classes' => \Doctrine\ODM\MongoDB\Configuration::AUTOGENERATE_EVAL,

        'hydrator_dir' => storage_path('odm/hydrators'),
        'hydrator_namespace' => 'Hydrators',
        'auto_generate_hydrator_classes' => \Doctrine\ODM\MongoDB\Configuration::AUTOGENERATE_EVAL,

        'persistent_collection_dir' => storage_path('odm/collections'),
        'persistent_collection_namespace' => 'PersistentCollections',
        'auto_generate_persistent_collection_classes' => \Doctrine\ODM\MongoDB\Configuration::AUTOGENERATE_EVAL,

        'use_transactional_flush' => false,

        'connection' => [
            'dsn' => sprintf('mongodb://%s:%d', env('MONGO_HOST','127.0.0.1'), env('MONGO_PORT', 27017)),
            'auth' => [
                'username' => env('MONGO_USER', ''),
                'password' => env('MONGO_PASS', ''),
                'database' => env('MONGO_DB', 'default'),
            ],
            'options' => [
                'typeMap' => DocumentManager::CLIENT_TYPEMAP,
            ],
        ],
        'default_db' => env('MONGO_DB', 'default'),
    ],
];
