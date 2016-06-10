<?php

namespace zaboy\scheduler\Scheduler\Factory;

use zaboy\scheduler\FactoryAbstract;
use zaboy\scheduler\Scheduler\Scheduler;
use zaboy\scheduler\Scheduler\SchedulerException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates if can and returns an instance of class 'Scheduler'
 *
 * For correct work the config must contain part below (services names must be not changed!!)
 * <code>
 * 'factories' => [
 *     // ...
 *     'timeline_datastore' => 'zaboy\scheduler\DataStore\Factory\TimelineFactory',
 *     'filters_datastore' => 'zaboy\scheduler\DataStore\Factory\FilterDataStoreFactory',
 *     'callback_manager' => 'zaboy\scheduler\Callback\Factory\CallbackManagerFactory',
 * ]
 * </code>
 *
 * Class ScriptAbstractFactory
 * @package zaboy\scheduler\Callback\Factory
 */
class SchedulerFactory extends FactoryAbstract
{
    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        if (!$serviceLocator->has('filters_datastore')) {
            throw new SchedulerException("Can't create datastore of filters because it's not described in config.");
        }
        /** @var \zaboy\rest\DataStore\DataStoreAbstract $filterDs */
        $filterDs = $serviceLocator->get('filters_datastore');

        if (!$serviceLocator->has('timeline_datastore')) {
            throw new SchedulerException("Can't create datastore of timeline because it's not described in config.");
        }
        /** @var  \zaboy\scheduler\DataStore\Timeline $timelineDs */
        $timelineDs = $serviceLocator->get('timeline_datastore');

        if (!$serviceLocator->has('callback_manager')) {
            throw new SchedulerException("Can't create callback manager because it's not described in config.");
        }
        /** @var \zaboy\scheduler\Callback\CallbackManager $callbackManager */
        $callbackManager = $serviceLocator->get('callback_manager');
        $instance = new Scheduler($filterDs, $timelineDs, $callbackManager);
        return $instance;
    }
}