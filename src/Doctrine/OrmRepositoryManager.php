<?php

namespace Phactor\Laminas\Doctrine;

use Phactor\Doctrine\OrmRepository;
use Phactor\ReadModel\Repository;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

class OrmRepositoryManager extends AbstractPluginManager
{
    /**
     * Whether or not to auto-add a FQCN as an invokable if it exists.
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;

    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = Repository::class;

    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        $config['abstract_factories'][] = new class() implements AbstractFactoryInterface
        {
            public function canCreate(ContainerInterface $container, $requestedName)
            {
                //Perhaps add a check that the requested name is a valid entity
                return true;
            }

            public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
            {
                return new OrmRepository($requestedName, $container->get(EntityManager::class));
            }
        };
        parent::__construct($configInstanceOrParentLocator, $config);
    }
}
