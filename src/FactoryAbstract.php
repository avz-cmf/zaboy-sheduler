<?php

namespace zaboy\scheduler;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

abstract class FactoryAbstract implements FactoryInterface
{
    /**
     * Alias for "createService"
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    abstract public function __invoke(ContainerInterface $container);

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