<?php

namespace Phactor\Laminas\ControllerPlugin;

use Phactor\Identity\Generator;
use Phactor\Message\MessageFirer;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MessageBusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $messageBus = $container->get('MessageBus');
        $config = $container->get('Config')['phactor'];
        $identityGenerator = $container->get($config['generator_service']);

        return new MessageBus(new MessageFirer($identityGenerator, $messageBus));
    }
}
