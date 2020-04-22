<?php

namespace Phactor\Laminas;

use Phactor\Message\Handler;
use Laminas\ServiceManager\AbstractPluginManager;

class MessageHandlerManager extends AbstractPluginManager
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
    protected $instanceOf = Handler::class;
}
