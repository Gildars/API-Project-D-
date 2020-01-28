<?php

return [
    'user' => [
        'model' => 'App\Models\User',
        'foreignKey' => null,
        'ownerKey' => null,
    ],
    'broadcast' => [
        'enable' => true,
        'app_name' => 'your-app-name',
        'pusher' => [
            'app_id' => '869852',
            'app_key' => 'efe72024de3826c708a4',
            'app_secret' => 'f61c40f3ade163647798',
            'options' => [
                'cluster' => 'eu',
                'encrypted' => true
            ]
        ],
    ],
    'oembed' => [
        'enabled' => false,
        'url' => '',
        'key' => ''
    ]
];
