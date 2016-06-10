<?php

namespace zaboy\scheduler\Callback\Factory;

use zaboy\scheduler\AbstractFactory;
use zaboy\scheduler\Callback\CallbackManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class CallbackManagerFactory extends AbstractFactory
{
    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        return new CallbackManager($serviceLocator);
    }
}