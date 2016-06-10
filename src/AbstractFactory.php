<?php

namespace zaboy\scheduler;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractFactory implements FactoryInterface
{
    /**
     * Alias for "createService"
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    abstract public function __invoke(ServiceLocatorInterface $serviceLocator);

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator);
    }

}