<?php

namespace zaboy\test\scheduler;

use zaboy\scheduler\Ticker;
use zaboy\scheduler\UTCTime;

class SchedulerTest extends \PHPUnit_Framework_TestCase
{
    protected $callbackServiceName = 'staticmethod_tick_callback';

    /** @var  \Zend\ServiceManager\ServiceManager $container */
    protected $container;

    /** @var  \zaboy\rest\DataStore\DataStoreAbstract $filterDs */
    protected $filterDs;

    /** @var  \zaboy\rest\DataStore\DataStoreAbstract $log */
    protected $log;

    public function setUp()
    {
        $this->container = include './config/container.php';
        $this->filterDs = $this->container->get('test_scheduler_filters_datastore');

        $this->log = $this->container->get('tick_log_datastore');
        $this->log->deleteAll();

        $this->setFilters();
    }

    protected function setFilters()
    {
        $this->filterDs->deleteAll();
        $filterData = [
            'rql' => 'or(eq(seconds,3),eq(seconds,8),eq(seconds,10),eq(seconds,15),eq(seconds,20),eq(seconds,23),eq(seconds,33),eq(seconds,41),eq(seconds,55),eq(seconds,59))',
            'callback' => 'tick_callback',
            'active' => 1
        ];
        $this->filterDs->create($filterData);

        $filterData = [
            'rql' => 'or(eq(seconds,4),eq(seconds,9),eq(seconds,11),eq(seconds,16),eq(seconds,21),eq(seconds,24),eq(seconds,34),eq(seconds,42),eq(seconds,56))',
            'callback' => 'tick_callback',
            'active' => 1
        ];
        $this->filterDs->create($filterData);
    }

    /**
     * @param array $options
     * @return Ticker
     */
    protected function setTicker($options = [])
    {
        $config = $this->container->get('config')['test_schedule_callback'];
        $hopCallback = $this->container->get($config['hop']['callback']);
        $tickCallback = $this->container->get($config['tick']['callback']);

        // Command line options have higher priority
        $options = array_merge($config, $options);
        $ticker = new Ticker($tickCallback, $hopCallback, $options);
        return $ticker;
    }

    public function test_countCallingCallback()
    {
        $this->setTicker()
            ->start();
        $this->assertEquals(19, $this->log->count());
    }
}