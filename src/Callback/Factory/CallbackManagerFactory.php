<?php

namespace zaboy\scheduler\Callback\Factory;

use zaboy\scheduler\FactoryAbstract;
use zaboy\scheduler\Callback\CallbackManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class CallbackManagerFactory extends FactoryAbstract
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