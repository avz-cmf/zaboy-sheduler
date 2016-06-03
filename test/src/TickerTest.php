<?php

namespace zaboy\test\sheduler;

class TickerTest extends \PHPUnit_Framework_TestCase
{

    protected $runCmdPattern;

    /** @var  \zaboy\rest\DataStore\DataStoreAbstract */
    protected $tickLog;

    /** @var  \zaboy\rest\DataStore\DataStoreAbstract */
    protected $hopLog;
    protected $config;

    protected function setUp()
    {
        $container = include './config/container.php';
        $dataStoreConfig = $container->get('config')['dataStore'];
        // Create log files if they do not exist
        if (!is_file($dataStoreConfig['hop_log_datastore']['filename'])) {
            copy(
                $dataStoreConfig['hop_log_datastore']['filename'] . '.dist',
                $dataStoreConfig['hop_log_datastore']['filename']
            );
        }
        if (!is_file($dataStoreConfig['tick_log_datastore']['filename'])) {
            copy(
                $dataStoreConfig['tick_log_datastore']['filename'] . '.dist',
                $dataStoreConfig['tick_log_datastore']['filename']
            );
        }

        $this->runCmdPattern = "php ";
        $this->runCmdPattern .= realpath(dirname(__DIR__) . '/../www/run.php');
        $this->runCmdPattern .= " -total_time %d -step %s";
        $this->runCmdPattern .= " &";

        $this->tickLog = $container->get('tick_log_datastore');
        $this->hopLog = $container->get('hop_log_datastore');
        $this->config = $container->get('config')['ticker'];
    }

    public function test_tickerInTenthsPartOfSeconds()
    {
        $this->tickLog->deleteAll();
        $this->hopLog->deleteAll();

        $totalTime = 3;

        $cmd = sprintf($this->runCmdPattern, $totalTime, 0.1);
        pclose(
            popen($cmd, 'w')
        );
        sleep($totalTime + 1);

        $this->assertEquals(30, $this->tickLog->count());
        $this->assertEquals(1, $this->hopLog->count());
    }

    public function test_tickerWithBigStep()
    {
        $this->tickLog->deleteAll();
        $this->hopLog->deleteAll();

        $totalTime = 10;

        $cmd = sprintf($this->runCmdPattern, $totalTime, 5);
        pclose(
                popen($cmd, 'w')
        );
        sleep($totalTime + 1);

        $this->assertEquals(2, $this->tickLog->count());
        $this->assertEquals(1, $this->hopLog->count());
    }

    public function test_clearLog()
    {
        // Add limits for log files
        $cmd = rtrim($this->runCmdPattern, '&');
        $cmd .= " -tick__max_log_rows %d";
        $cmd .= " -hop__max_log_rows %d";
        $cmd .= ' &';

        $totalTime = 3;
        // Do not clear log files after previous test
        $cmd = sprintf($cmd, $totalTime, 0.1, 30, 1);
        pclose(
            popen($cmd, 'w')
        );
        sleep($totalTime + 1);

        $this->assertEquals(
            30, $this->tickLog->count()
        );
        $this->assertEquals(
            1, $this->hopLog->count()
        );
    }

}
