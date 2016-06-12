<?php

namespace zaboy\test\scheduler\Scheduler\Factory;

use zaboy\scheduler\Scheduler\Scheduler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SchedulerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $filterDs = $serviceLocator->get('test_scheduler_filters_datastore');
        $timelineDs = $serviceLocator->get('timeline_datastore');
        $instance = new Scheduler($filterDs, $timelineDs);
        return $instance;
    }

}