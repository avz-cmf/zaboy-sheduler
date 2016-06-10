<?php

namespace zaboy\scheduler\Callback\Factory;

use zaboy\scheduler\Callback\CallbackManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CallbackManagerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CallbackManager($serviceLocator);
    }

}