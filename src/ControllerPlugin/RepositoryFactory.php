<?php

namespace Phactor\Laminas\ControllerPlugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config')['phactor'];
        return new Repository($container->get($config['readmodel_repository_service']));
    }
}
