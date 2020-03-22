<?php
namespace TBoxRabbitMQReceiver\Receiver;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use TBoxRabbitMQReceiver\StorageAdapter\StorageAdapterInterface;
use TBoxRabbitMQReceiver\StorageAdapter\StorageAdapterFactory;


class RabbitMQReceiver
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;
    
    /**
     * @var AMQPChannel 
     */
    protected $channel;
    
    /**
     * @var string
     */
    protected $queueName;
    
    /**
     * @var StorageAdapterInterface
     */
    protected $storageAdapter;
    
    
    public function __construct($Config, $Queue)
    {
        if (empty($Config['amqp']['server'])) {
            throw new \Exception('RabbitMQ server settings missing from config');
        }
        $required = ['host', 'port', 'username', 'password'];
        foreach ($required as $item) {
            if (empty($Config['amqp']['server'][$item])) {
                throw new \Exception('RabbitMQ server ' . $item . ' missing from config');
            }
        }
        
        if (empty($Config['rabbitmq_receiver_storage'][$Queue])) {
            throw new \Exception('RabbitMQ storage settings missing for provided queue');
        }
        $storageAdapterFactory = new StorageAdapterFactory();
        $this->storageAdapter = $storageAdapterFactory($Config['rabbitmq_receiver_storage'][$Queue]);
        
        extract($Config['amqp']['server']);
        $this->connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channel = $this->connection->channel();
        $this->queueName = $Queue;
    }
    
    
    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    
    /**
     * Execute
     */
    public function run()
    {
        $this->channel->queue_declare($this->queueName);
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $this->channel->basic_consume($this->queueName, '', false, true, false, false, [$this, 'processMessage']);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }
    
    
    /**
     * Process received message
     * 
     * @param AMQPMessage $Message
     */
    public function processMessage(AMQPMessage $Message)
    {
        $message = $Message->body;
        echo ' [ ] Received ', $message, "\n";
        
        if (!$this->storageAdapter->validate($message)) {
            echo ' [!] ', $this->storageAdapter->getValidationError(), "\n";
            return;
        }
        
        $result = $this->storageAdapter->persist($message);
        if ($result) {
            echo ' [v] Successfully saved ', "\n";
        } else {
            echo ' [!] ', $this->storageAdapter->getPersistError(), "\n";
        }
    }
}