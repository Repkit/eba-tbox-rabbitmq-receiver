<?php
namespace TBoxRabbitMQReceiver;

class Module
{
    public function getConfig()
    {
        $moduleConf = include __DIR__ . '/../config/module.config.php';
        $routesConf = include __DIR__ . '/../config/routes.global.php';
        return array_merge($moduleConf, $routesConf);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
