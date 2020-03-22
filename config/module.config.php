<?php
return [
    'service_manager' => [
        'factories' => [
            \TBoxRabbitMQReceiver\Receiver\RabbitMQReceiverCli::class => \TBoxRabbitMQReceiver\Receiver\RabbitMQReceiverCliFactory::class,
        ],
    ],
];
