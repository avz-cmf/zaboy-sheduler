<?php

namespace zaboy\scheduler\DataStore\Factory;

use zaboy\scheduler\FactoryAbstract;
use Zend\ServiceManager\ServiceLocatorInterface;
use zaboy\scheduler\DataStore\Timeline;

class TimelineFactory extends FactoryAbstract
{
    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        return new Timeline();
    }
}