<?php
namespace TBoxRabbitMQReceiver\Receiver;


class RabbitMQReceiverCliFactory
{
    public function __invoke($Services)
    {
        $config = $Services->get('config');
        
        return new RabbitMQReceiverCli($config);
    }
}