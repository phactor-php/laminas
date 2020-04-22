<?php

namespace Phactor\Laminas\ControllerPlugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Psr\Container\ContainerInterface;

class Repository extends AbstractPlugin
{
    private $repositoryManager;

    public function __construct(ContainerInterface $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    public function __invoke(string $className)
    {
        return $this->repositoryManager->get($className);
    }
}
