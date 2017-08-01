<?php

return [
    'defaults' => [
        'connection' => 'default',
        'exchange' => 'default',
        'queue' => 'default',
    ],

    'connections' => [
        'default' => [
            'host' => env('AMQP_HOST', 'localhost'),
        ],
    ],

    'exchanges' => [
        'default' => [
            'connection' => 'default',
            'name'       => 'exchange_name',
            'type'       => 'topic',
        ],
    ],

    'queues' => [
        'default' => [
            'connection' => 'default',
            'bindings' => [
                ['exchange_name', 'routing_key'],
            ],
        ],
    ],

];