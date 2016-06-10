<?php

namespace zaboy\test\Callback;

class CallbackAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Zend\ServiceManager\ServiceManager $container */
    protected $container;

    /** @var  \zaboy\rest\DataStore\DataStoreAbstract */
    protected $log;

    protected function setUp()
    {
        $this->container = include './config/container.php';
        $this->log = $this->container->get('tick_log_datastore');
    }

    public function test_ScriptCallback()
    {
        // Clear log before testing
        $this->log->deleteAll();

        $callbackManager = $this->container->get('callback_manager');
        /** @var \zaboy\scheduler\Callback\Script $scriptCallback */
        $scriptCallback = $callbackManager->get('script_example_tick_callback');
        $options = [
            'param1' => 'value1',
            'param2' => ['value21', 'value22'],
        ];
        $scriptCallback->call($options);

        // Espected that in the log will be one entry
        $item = $this->log->read(1);
        $this->assertEquals(
            print_r($options, 1), $item['step']
        );

        // Clear log again
        $this->log->deleteAll();
    }
}