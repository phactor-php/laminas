<?php

namespace Phactor\Laminas;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Phactor\Doctrine\Dbal\JsonObject;
use Phactor\Doctrine\Mappings;
use Phactor\Doctrine\OrmEventStore;
use Phactor\Doctrine\OrmRepository;
use Phactor\Laminas\Doctrine\OrmEventStoreFactory;
use Phactor\Laminas\Doctrine\OrmRepositoryManager;
use Phactor\Laminas\Doctrine\OrmRepositoryManagerFactory;
use Phactor\Laminas\ReadModel\InMemoryRepositoryManagerFactory;
use Phactor\Identity\YouTubeStyleIdentityGenerator;
use Phactor\EventStore\InMemoryEventStore;
use Phactor\Laminas\ControllerPlugin\MessageBusFactory;
use Phactor\Laminas\ControllerPlugin\RepositoryFactory;
use Phactor\Laminas\ReadModel\InMemoryRepositoryManager;

class Module
{
    public function getConfig()
    {
        $config = [
            'service_manager' => [
                'factories' => [
                    InMemoryEventStore::class => InvokableFactory::class,
                    YouTubeStyleIdentityGenerator::class => InvokableFactory::class,
                    InMemoryRepositoryManager::class => InMemoryRepositoryManagerFactory::class,
                    'MessageBus' => BusFactory::class,
                    MessageHandlerManager::class => MessageHandlerManagerFactory::class,
                ]
            ],
            'controller_plugins' => [
                'factories' => [
                    'messageBus' => MessageBusFactory::class,
                    'repository' => RepositoryFactory::class,
                ]
            ],
            'phactor' => [
                'eventstore_service' => InMemoryEventStore::class,
                'readmodel_repository_service' => InMemoryRepositoryManager::class,
                'generator_service' => YouTubeStyleIdentityGenerator::class,
                'bus_logger_service' => null,
                'message_handlers' => [],
                'message_rbac' => [],
                'message_subscriptions' => [],
                'message_subscription_providers' => [],
            ],
        ];

        if (class_exists(\DoctrineORMModule\Module::class) && class_exists(OrmEventStore::class)) {
            $config['phactor']['eventstore_service'] = OrmEventStore::class;
            $config['phactor']['readmodel_repository_service'] = OrmRepository::class;
            $config['service_manager']['factories'][OrmEventStore::class] = OrmEventStoreFactory::class;
            $config['service_manager']['factories'][OrmRepositoryManager::class] = OrmRepositoryManagerFactory::class;
            $config['doctrine'] = [
                'configuration' => [
                    'orm_default' => [
                        'types' => [
                            'json_object' => JsonObject::class
                        ]
                    ]
                ],
                'connection' => [
                    'orm_default' => [
                        'doctrine_type_mappings' => [
                            'json_object' => 'json_object'
                        ],
                    ]
                ],
                'driver' => [
                    'phactor' => [
                        'class' => XmlDriver::class,
                        'cache' => 'array',
                        'paths' => [Mappings::XML_MAPPINGS]
                    ],
                    'orm_default' => [
                        'drivers' => [
                            'Phactor\Message' => 'phactor',
                            'Phactor\Actor' => 'phactor',
                            'Phactor\Doctrine\Entity' => 'phactor',
                        ]
                    ]
                ],
            ];
        }

        return $config;
    }
}
