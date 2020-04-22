<?php

namespace Phactor\Laminas;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class MessageHandlerManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config')['phactor']['message_handlers'];
        return new MessageHandlerManager($container, $config);
    }
}
