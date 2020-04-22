<?php

namespace Phactor\Laminas\Doctrine;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class OrmRepositoryManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OrmRepositoryManager($container, []);
    }
}
