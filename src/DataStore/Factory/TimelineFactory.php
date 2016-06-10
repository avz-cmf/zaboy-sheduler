<?php

namespace zaboy\scheduler\DataStore\Factory;

use zaboy\scheduler\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use zaboy\scheduler\DataStore\Timeline;

class TimelineFactory extends AbstractFactory
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