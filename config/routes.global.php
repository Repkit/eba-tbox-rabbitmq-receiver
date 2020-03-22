<?php

$routes = [
    [
        'name' => 'rabbitmq-receiver',
        'route' => '<queue>',
        'short_description' => 'Get messages from queue and store them',
        'handler' => \TBoxRabbitMQReceiver\Receiver\RabbitMQReceiverCli::class,
    ]
];

return array(
    'cli-routes' => $routes
);