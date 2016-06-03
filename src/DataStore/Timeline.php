<?php

namespace zaboy\scheduler\DataStore;

use zaboy\scheduler\DataStore\TimelineDataStoreException;
use zaboy\scheduler\DataStore\Iterator\TimelineIterator;
use zaboy\scheduler\UTCTime;
use Xiag\Rql\Parser\Node\Query\LogicOperator;
use Xiag\Rql\Parser\Node\Query\ScalarOperator;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use zaboy\rest\DataStore\DataStoreAbstract;
use zaboy\rest\DataStore\ConditionBuilder\PhpConditionBuilder;

class Timeline extends DataStoreAbstract
{
    const DEFAULT_STEP = 1;

    /**
     * From what value of time it's needed to build timeline
     * @var int
     */
    protected $from;

    /**
     * Number of values in timeline
     * @var
     */
    protected $number = null;

    /**
     * Step of timeline in seconds
     * @var int
     */
    protected $step = self::DEFAULT_STEP;

    public function __construct()
    {
        $this->conditionBuilder = new PhpConditionBuilder();
        $this->setFrom();
    }

    /**
     * @param null $from
     */
    protected function setFrom($from = null)
    {
        // By default sets CURRENT_TIMESTAMP
        if (is_null($from)) {
            $from = UTCTime::getUTCTimestamp(0);
        }
        $this->from = $from;
    }

    /**
     * @param $number
     * @throws TimelineDataStoreException
     */
    protected function setNumber($number)
    {
        if (!is_null($number) && $number <= 0) {
            throw new TimelineDataStoreException("The \"number\" value must be greater than zero");
        }
        $this->number = $number;
    }

    /**
     * @param $step
     * @throws TimelineDataStoreException
     */
    protected function setStep($step)
    {
        if ($step <= 0) {
            throw new TimelineDataStoreException("The \"step\" value must be greater than zero");
        }
        if (is_null($step)) {
            $step = self::DEFAULT_STEP;
        }
        $this->step = $step;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function has($id)
    {
        $this->checkIdentifierType($id);
        // We believe that it isn't allow to pass less than one millisecond here
        // Cut the shake of accuracy
        $id = round($id * 1000) / 1000;
        // One digital after comma
        $id = floatval($id) * 10;
        $diff = $id - floor($id);
        return ($diff == 0);
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function read($id)
    {
        if (!$this->has($id)) {
            return null;
        }
        $id = round(floatval($id), 1);
        $timestamp = intval($id);
        $tpSeconds = round(($id - $timestamp) * 10);
        // parses timestamp for parts of date
        $date = getdate($timestamp);
        // adds needed elements
        $date['id'] = $id;
        $date['tp_seconds'] = $tpSeconds;
        $date['timestamp'] = $date[0];
        unset($date[0]);
        return $date;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function getIterator()
    {
        return new TimelineIterator([$this, 'read'], $this->from, $this->number, $this->step);
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    protected function checkIdentifierType($id)
    {
        if (!is_numeric($id)) {
            throw new TimelineDataStoreException("Type of Identifier is wrong - " . gettype($id));
        }
        $id = floatval($id);
        if (abs($id) >= PHP_INT_MAX) {
            throw new TimelineDataStoreException("Specified id=\"{$id}\" is out of range for Unix-time");
        }
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function query(Query $query)
    {
        $this->determineStep($query);
        $this->determineFrom($query);
        return parent::query($query);
    }

    /**
     * Determines the "from" and "number" values from rql query analizing nodes for field "timestamp"
     * @param Query $query
     */
    public function determineFrom(Query $query)
    {
        $rootNode = $query->getQuery();
        // If RQL-query match pattern 'ge|gt|le|lt|eq(timestamp,<timestamp>)'
        if ($rootNode instanceof AbstractScalarOperatorNode) {
            $this->analizeNode($rootNode);
            return;
        }
        if ($rootNode instanceof LogicOperator\AndNode) {
            foreach ($rootNode->getQueries() as $node) {
                $this->analizeNode($node);
            }
        }
    }

    /**
     * Analizes node for timestamp value and sets properties "from" and/or "number" from it
     * @param AbstractQueryNode $rootNode
     * @throws TimelineDataStoreException
     */
    protected function analizeNode(AbstractQueryNode $rootNode)
    {
        // If RQL-query match pattern 'and(<restRqlQuery>,ge|gt(timestamp,<timestamp>))', use this value for 'from'
        if (in_array($rootNode->getNodeName(), ['ge', 'gt'])) {
            if ('timestamp' == $rootNode->getField()) {
                $from = $rootNode->getValue();
            }
        }
        // If RQL-query match pattern 'and(<restRqlQuery>,le|lt(timestamp,<timestamp>))', use this value for 'number'
        if (in_array($rootNode->getNodeName(), ['le', 'lt'])) {
            if ('timestamp' == $rootNode->getField()) {
                $to = $rootNode->getValue();
            }
        }
        // If RQL-query match pattern 'and(<restRqlQuery>,eq(timestamp,<timestamp>))',
        // use this value for both 'from' and 'number'
        if ('eq' == $rootNode->getNodeName()) {
            if ('timestamp' == $rootNode->getField()) {
                $from = $rootNode->getValue();
                $to = $rootNode->getValue();
            }
        }
        // Do not overwrite setted values if values gotten from query are null
        if (!empty($from)) {
            $this->setFrom($from);
        }
        if (!empty($to)) {
            if (($to - $this->from) < $this->step) {
                $this->setNumber(1);
            } else {
                $this->setNumber(floor(($to - $this->from) / $this->step));
            }
        }
    }

    /**
     * Determines step size from rql query finding all time labels and find min weight from them
     * @param Query $query
     * @throws TimelineDataStoreException
     */
    protected function determineStep(Query $query)
    {
        // TODO: When Query-class will have a method which will build rql-string from rql-object this vethod will require refactoring
        $timeWeights = $this->collectAllTimeWeights($query->getQuery());
        if (!count($timeWeights)) {
            $this->setStep(self::DEFAULT_STEP);
        } else {
            $this->setStep(min($timeWeights));
        }
    }

    /**
     * Collects time labels from rql query via recursive search them in its nodes
     * @param AbstractQueryNode|null $rootNode
     * @return array
     */
    protected function collectAllTimeWeights(AbstractQueryNode $rootNode = null)
    {
        /**
         * Array of time weight for determining minimum step for timeline (its iterator)
         * @var array
         */
        $timeWeights = [
            'id' => 0.1,
            'tp_seconds' => 0.1,
            'seconds' => 1,
            'minutes' => 60,
            'hours' => 60,
            'mday' => 60,
            'wday' => 60,
            'yday' => 60,
            'weekday' => 60,
            'mon' => 60,
            'month' => 60,
            'year' => 60,
        ];
        // Compound statement, f.e. and|or
        if (method_exists($rootNode, 'getQueries')) {
            $weights = [];
            foreach ($rootNode->getQueries() as $node) {
                // Dive on lower level and find there
                $nodeTimeWeights = $this->collectAllTimeWeights($node);
                $weights = array_merge($weights, $nodeTimeWeights);
            }
            return $weights;
        }
        // If exists time label in node?
        if (method_exists($rootNode, 'getField')) {
            $field = $rootNode->getField();
            if (isset($timeWeights[$field])) {
                return (array) $timeWeights[$field];
            }
        }
        // uncertainty
        return [];
    }

    /**
     * @param $methodName
     * @throws TimelineDataStoreException
     */
    protected function throwException($methodName)
    {
        throw new TimelineDataStoreException("The DataStore type \"Timeline\" doesn't allow to work with method \"$methodName\"");
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function create($itemData, $rewriteIfExist = false)
    {
        $this->throwException('create');
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function update($itemData, $createIfAbsent = false)
    {
        $this->throwException('update');
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function delete($id)
    {
        $this->throwException('delete');
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function deleteAll()
    {
        $this->throwException('deleteAll');
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function count()
    {
        $this->throwException('count');
    }
}