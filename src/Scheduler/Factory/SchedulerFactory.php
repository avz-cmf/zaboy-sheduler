<?php

namespace zaboy\scheduler\Scheduler\Factory;

use Interop\Container\ContainerInterface;
use zaboy\scheduler\Callback\CallbackManager;
use zaboy\scheduler\FactoryAbstract;
use zaboy\scheduler\Scheduler\Scheduler;
use zaboy\scheduler\Scheduler\SchedulerException;

/**
 * Creates if can and returns an instance of class 'Scheduler'
 *
 * For correct work the config must contain part below (services names must be not changed!!)
 * <code>
 * 'factories' => [
 *     // ...
 *     'timeline_datastore' => 'zaboy\scheduler\DataStore\Factory\TimelineFactory',
 *     'filters_datastore' => 'zaboy\scheduler\DataStore\Factory\FilterDataStoreFactory',
 *     'callback_manager' => 'zaboy\scheduler\Callback\Factory\CallbackManagerFactory',     // may absents; will create from default class
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
    public function __invoke(ContainerInterface $container)
    {
        if (!$container->has('filters_datastore')) {
            throw new SchedulerException("Can't create datastore of filters because it's not described in config.");
        }
        /** @var \zaboy\rest\DataStore\DataStoreAbstract $filterDs */
        $filterDs = $container->get('filters_datastore');

        if (!$container->has('timeline_datastore')) {
            throw new SchedulerException("Can't create datastore of timeline because it's not described in config.");
        }
        /** @var  \zaboy\scheduler\DataStore\Timeline $timelineDs */
        $timelineDs = $container->get('timeline_datastore');

        if ($container->has(CallbackManager::SERVICE_NAME)) {
            /** @var \zaboy\scheduler\Callback\CallbackManager $callbackManager */
            $callbackManager = $container->get(CallbackManager::SERVICE_NAME);
        } else {
            $callbackManager = new CallbackManager($container);
        }

        $instance = new Scheduler($filterDs, $timelineDs, $callbackManager);
        return $instance;
    }
}