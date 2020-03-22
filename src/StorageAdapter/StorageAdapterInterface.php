<?php
namespace TBoxRabbitMQReceiver\StorageAdapter;

interface StorageAdapterInterface
{
    /**
     * @param string $Message
     * @return boolean
     */
    public function validate($Message);
    
    /**
     * @return string
     */
    public function getValidationError();
    
    /**
     * @param string $Message
     */
    public function persist($Message);
    
    /**
     * @return string
     */
    public function getPersistError();
}
