<?php

return [
    'defaults' => [
        'connection' => env('AMQP_CONNECTION', 'rabbitmq'),
        'exchange' => env('AMQP_EXCHANGE', 'amq.direct'),
        'queue' => env('AMQP_EXCHANGE', 'amq.direct'),
    ],

    'connections' => [
        'rabbitmq' => [
            'host' => env('AMQP_HOST', 'localhost'),
        ],
    ],

    'exchanges' => [
        'amq.direct' => [
            'name' => 'amq.direct',
            'type' => 'direct',
        ],
    ],

    'queues' => [
        
    ],

];