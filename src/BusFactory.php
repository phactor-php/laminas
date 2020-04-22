<?php

namespace Phactor\Laminas;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Stdlib\ArrayUtils;
use Phactor\Actor\Repository;
use Phactor\Actor\Router;
use Phactor\Actor\Subscription\Subscriber;
use Phactor\Actor\Subscription\Subscription;
use Phactor\Actor\Subscription\SubscriptionHandler;
use Phactor\Message\Dispatcher\All;
use Phactor\Message\Dispatcher\Authorise;
use Phactor\Message\Dispatcher\Authorise\User;
use Phactor\Message\Dispatcher\Capture;
use Phactor\Message\Dispatcher\Delay;
use Phactor\Message\Dispatcher\Delay\DeferredMessage;
use Phactor\Message\Dispatcher\Lazy;
use Phactor\Message\Dispatcher\Queue;
use Phactor\Message\ExtractSubscriptions;
use Phactor\Message\HasSubscriptions;
use Phactor\Message\MessageSubscriptionProvider;

class BusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config')['phactor'];

        $eventStore = $container->get($config['eventstore_service']);
        $readmodelRepositoryContainer = $container->get($config['readmodel_repository_service']);
        $delayedMessageRepository = $readmodelRepositoryContainer->get(DeferredMessage::class);

        $all = new All();
        $bus = new Capture(
            new Authorise(
                new Delay(
                    new Queue($all),
                    $delayedMessageRepository,
                    $eventStore
                ),
                $config['message_rbac'],
                $container->get(User::class)
            )
        );

        if (isset($config['actors'])) {
            $generator = $container->get($config['generator_service']);
            $subscriptionRepository = $readmodelRepositoryContainer->get(Subscription::class);
            $repository = new Repository($bus, $eventStore, $generator, new Subscriber($subscriptionRepository));
            $all->append(new Router($repository, ...$config['actors']));
            $all->append(new SubscriptionHandler($subscriptionRepository, $repository));
        }

        $logger = null;
        if ($config['bus_logger_service'] !== null) {
            $logger = $container->get($config['bus_logger_service']);
        }

        $all->append(new Lazy($this->getSubscriptions($config), $container->get(MessageHandlerManager::class), $logger));

        return $bus;
    }

    private function getSubscriptions($config): array
    {
        $subscriptions = $config['message_subscriptions'];

        foreach ($config['message_subscription_providers'] as $provider) {
            $interfaces = class_implements($provider);
            switch (true) {
                case in_array(MessageSubscriptionProvider::class, $interfaces):
                    $providerInstance = new $provider();
                    $newSubscriptions = $providerInstance->getSubscriptions();
                    break;
                case in_array(HasSubscriptions::class, $interfaces):
                    $providerInstance = new ExtractSubscriptions($provider);
                    $newSubscriptions = $providerInstance->getSubscriptions();
                    break;
            }

            $subscriptions = ArrayUtils::merge($subscriptions, $newSubscriptions);
        }

        return $subscriptions;
    }
}
