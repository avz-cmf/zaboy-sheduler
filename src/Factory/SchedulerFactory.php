<?php

namespace zaboy\scheduler\Factory;

use zaboy\scheduler\Scheduler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SchedulerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $filterDs = $serviceLocator->get('filters_datastore');
        $timelineDs = $serviceLocator->get('timeline_datastore');
        $instance = new Scheduler($filterDs, $timelineDs);
        return $instance;
    }

}