<?php

namespace zaboy\test\scheduler;


use zaboy\scheduler\DataStore\Timeline;
use zaboy\scheduler\UTCTime;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\Query\LogicOperator;
use Xiag\Rql\Parser\Node\Query\ScalarOperator;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;

class TimelineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /** @var  \zaboy\scheduler\DataStore\Timeline */
    protected $object;

    protected function setUp()
    {
        $this->container = include './config/container.php';
        $this->object = $this->container->get('timeline_datastore');
    }

    protected function parseRql($rql)
    {
        $lexer = new Lexer();
        $parser = Parser::createDefault();
        $tokens = $lexer->tokenize($rql);
        /* @var $rqlQueryObject \Xiag\Rql\Parser\Query */
        $rqlQueryObject = $parser->parse($tokens);
        return $rqlQueryObject;
    }

    public function test_hasValue()
    {
        $this->assertTrue(
            $this->object->has(123)
        );
        $this->assertTrue(
            $this->object->has(123.1)
        );
        $this->assertFalse(
            $this->object->has(123.099)
        );
        $this->assertTrue(
            $this->object->has(123.0)
        );
        $this->setExpectedExceptionRegExp('zaboy\scheduler\DataStore\Exception\TimelineDataStoreException',
            "/Specified id=\"[0-9.]+\" is out of range for Unix-time/");
        $this->object->has(PHP_INT_MAX + 0.01);
    }

    public function test_read()
    {
        $this->assertNull($this->object->read(0.99));

        $id = UTCTime::getUTCTimestamp();
        $timestamp = intval($id);
        $tpSeconds = round(($id - $timestamp) * 10);
        $date = getdate($timestamp);
        $date['id'] = $id;
        $date['tp_seconds'] = $tpSeconds;
        $date['timestamp'] = $date[0];
        unset($date[0]);
        $this->assertEquals($date, $this->object->read($date['id']));
    }

    public function _test_queryGetTimelineFromEmptyQuery()
    {
        $this->setExpectedExceptionRegExp('zaboy\scheduler\DataStore\Exception\TimelineDataStoreException',
            "/The maximum count of items was reached/");

        $query = new Query();
        $this->object->query($query);
    }

    public function test_queryGetResultFromQueryWithoutSpecifyingTimeTypes()
    {
        $from = UTCTime::getUTCTimestamp(0);
        $to = $from + 60;
        $rql = "and(ge(timestamp,{$from}),le(timestamp,{$to}))";
        $rqlQueryObject = $this->parseRql($rql);

        $rows = $this->object->query($rqlQueryObject);
        $this->assertEquals(60, count($rows));
    }

    public function test_queryWithoutAndNodeAndWithTimestamp()
    {
        $from = UTCTime::getUTCTimestamp(0) + 10;
        $rql = "le(timestamp,{$from})";
        $rqlQueryObject = $this->parseRql($rql);

        $rows = $this->object->query($rqlQueryObject);
        $this->assertEquals(10, count($rows));
    }

    public function test_queryCountStepsFromUnmultipleRange1()
    {
        $from = UTCTime::getUTCTimestamp(0);
        $to = $from + 58;
        $minute = date('i', $from);
        $rql = "and(or(eq(minutes," . $minute . "),eq(minutes," . ($minute + 1) . ")),lt(timestamp,{$to}))";
        $rqlQueryObject = $this->parseRql($rql);

        $rows = $this->object->query($rqlQueryObject);
        $this->assertEquals(1, count($rows));
    }

    public function test_getRowsByIds()
    {
        $id = UTCTime::getUTCTimestamp();
        $to = UTCTime::getUTCTimestamp(0) + 1;

        $rql = "and(or(eq(id,{$id}),eq(id," . ($id + 0.5) . ")),le(timestamp,{$to}))";
        $rqlQueryObject = $this->parseRql($rql);
        $rows = $this->object->query($rqlQueryObject);

        $this->assertEquals(2, count($rows));
    }

    public function test_enumerationTimelineWithoutUsnigQuery()
    {
        // Checks that iterator won't go to infinity
        $count = 0;
        foreach ($this->object as $value) {
            if (++$count == 10) {
                break;
            }
        }
        $this->assertEquals(10, $count);
    }

    public function test_compareTimelinesWithQuery()
    {
        $from = UTCTime::getUTCTimestamp(0);

        $timeline = [];
        // step is 3 seconds
        $rql = "and(or(eq(seconds,";
        for ($i = 0; $i < 15; $i += 3) {
            $dateInfo = $this->object->read($from + $i);
            $timeline[] = $dateInfo;
            $rql .= $dateInfo['seconds'];
            $rql .= "),eq(seconds,";
        }
        $rql = rtrim($rql, "),eq(seconds,") . ")),ge(timestamp," . $from . "))&limit(5,0)";
        $rqlQueryObject = $this->parseRql($rql);
        $timelineDsRows = $this->object->query($rqlQueryObject);

        $this->assertEquals($timeline, $timelineDsRows);
    }
}