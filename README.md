Client installation:

1) Add TBoxRabbitMQReceiver to modules.cli.config.php

2) Add amqp-storage.local.php to config/autoload

3) Verify amqp.global.php settings in config/autoload

4) Start listener with "php eba/clients/[client]/public/index.php  rabbitmq-receiver queue_name