<?php

namespace zaboy\test\scheduler\Iterator;

use zaboy\scheduler\DataStore\Iterator\TimelineIterator;

class TimelineIteratorTest extends \PHPUnit_Framework_TestCase
{
    protected $iteratorCallbackName = 'zaboy\test\scheduler\Iterator\TimelineIteratorTest::iteratorCallback';

    public static function iteratorCallback($id)
    {
        switch ($id) {
            case 10:
                $text = 'one';
                break;
            case 20:
                $text = 'two';
                break;
            case 30:
                $text = 'three';
                break;
            default:
                $text = "I don't know...";
                break;
        }
        return ['id' => $id, 'text' => $text];
    }

    public function test_createAnIterator()
    {
        $now = time() * 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, 1000, 10);
        $this->assertTrue(is_a($iterator, 'zaboy\scheduler\DataStore\Iterator\TimelineIterator', true));
    }

    public function test_createAnIteratorWithWrongStep0()
    {
        $now = time() * 10;
        $this->setExpectedExceptionRegExp('zaboy\scheduler\DataStore\Exception\TimelineDataStoreException',
            "/Paramter \"step\" must be greater than zero/");
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, 1000, 0);
    }

    public function test_checkRewindValue()
    {
        $now = time() * 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, null, 10);
        $iterator->rewind();
        $this->assertEquals($now, $iterator->key());
    }

    public function test_checkNextStepValue()
    {
        $now = time() * 10;
        $step = 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, null, $step);
        $iterator->rewind();
        $iterator->next();
        $this->assertEquals($now + $step, $iterator->key());
    }

    public function test_checkNextStepValueWithFloatStep()
    {
        $start = 0;
        $step = 0.1;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $start, null, $step);
        $iterator->rewind();
        $iterator->next();
        $this->assertEquals($start + $step, $iterator->key());
    }

    public function test_checkLimitOfItemsCount()
    {
        $now = time() * 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, null, 10);
        $this->setExpectedExceptionRegExp('zaboy\scheduler\DataStore\Exception\TimelineDataStoreException',
            "/The maximum count of items was reached/");
        foreach ($iterator as $step) {

        }
    }

    public function test_checkNormalWorking()
    {
        $now = time() * 10;
        $number = 100;
        $step = 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, $number, $step);
        foreach ($iterator as $id) {

        }
        $key = $now + $number * $step;
        $this->assertEquals($key, $iterator->key());
    }

    public function test_checkCountOfIterations()
    {
        $now = time() * 10;
        $number = 100;
        $step = 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $now, $number, $step);
        $count = 0;
        foreach ($iterator as $id) {
            $count++;
        }
        $this->assertEquals(100, $count);
    }

    public function test_checkCurrentValue()
    {
        $id = 10;
        $iterator = new TimelineIterator($this->iteratorCallbackName, $id, null, 10);
        $iterator->rewind();
        $this->assertEquals(['id' => $id, 'text' => 'one'], $iterator->current());
        $iterator->next();
        $id = $iterator->key();
        $this->assertEquals(['id' => $id, 'text' => 'two'], $iterator->current());
    }
}