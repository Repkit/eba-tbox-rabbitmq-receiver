<?php
namespace TBoxRabbitMQReceiver\Receiver;


class RabbitMQReceiverCli
{
    protected $config;
    
    public function __construct(array $Config)
    {
        $this->config = $Config;
    }
    
    public function __invoke(\ZF\Console\Route $Route)
    {
        $routeMatches = $Route->getMatches();
        $queue = $routeMatches['queue'];
        
        $rabbitmqReceiver = new RabbitMQReceiver($this->config, $queue);
        $rabbitmqReceiver->run();
    }
}