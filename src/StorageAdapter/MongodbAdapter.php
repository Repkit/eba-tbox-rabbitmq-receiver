<?php
namespace TBoxRabbitMQReceiver\StorageAdapter;

use TBoxRabbitMQReceiver\StorageAdapter\StorageAdapterInterface;


class MongodbAdapter implements StorageAdapterInterface
{
    public function __construct()
    {
        // TODO
    }
    
    public function validate($Message)
    {
        // TODO
    }
    
    public function getValidationError()
    {
        // TODO
    }
    
    public function persist($Message)
    {
        // TODO
    }
    
    public function getPersistError()
    {
        // TODO
    }
}