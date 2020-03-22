<?php
namespace TBoxRabbitMQReceiver\StorageAdapter;

class StorageAdapterFactory
{
    public function __invoke($Config)
    {
        if (!is_array($Config) || count($Config) !== 1) {
            throw new \Exception('Invalid config for storage adapter');
        }
        $adapterClass = array_keys($Config)[0];
        if (!class_exists($adapterClass)) {
            throw new \Exception('Storage adapter class ' . $adapterClass . ' not found');
        }
        return new $adapterClass(array_values($Config)[0]);
    }
}