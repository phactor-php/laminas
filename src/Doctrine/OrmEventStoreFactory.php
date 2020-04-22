<?php


namespace Phactor\Laminas\Doctrine;


use Phactor\Doctrine\OrmEventStore;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class OrmEventStoreFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OrmEventStore($container->get(EntityManager::class));
    }
}
