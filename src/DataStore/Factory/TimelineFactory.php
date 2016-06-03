<?php

namespace zaboy\scheduler\DataStore\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use zaboy\scheduler\DataStore\Timeline;

class TimelineFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Timeline();
    }
}