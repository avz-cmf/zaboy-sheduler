<?php

namespace zaboy\scheduler;

use zaboy\scheduler\DataStore\Timeline;
use Xiag\Rql\Parser\Query;
use zaboy\rest\DataStore\DataStoreAbstract;
use Xiag\Rql\Parser\Node\Query\ScalarOperator;
use Xiag\Rql\Parser\Node\Query\LogicOperator;
use Xiag\Rql\Parser\Lexer;
use Xiag\Rql\Parser\Parser;

class Scheduler
{
    /** @var \zaboy\rest\DataStore\DataStoreAbstract $filterDs */
    protected $filterDs;

    /** @var  \zaboy\scheduler\DataStore\Timeline $timelineDs */
    protected $timelineDs;

    /**
     * Scheduler constructor.
     */
    public function __construct(DataStoreAbstract $filterDs, Timeline $timelineDs)
    {
        $this->filterDs = $filterDs;
        $this->timelineDs = $timelineDs;
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

    protected function addTickLimit(Query $query, $tickId, $step)
    {
        $andNode = new LogicOperator\AndNode([
            $query->getQuery(),
            new ScalarOperator\GeNode('timestamp', $tickId),
            new ScalarOperator\LtNode('timestamp', $tickId + $step),
        ]);
        $query->setQuery($andNode);
        return $query;
    }

    public function runTick($tickId, $step)
    {
        $query = new Query();
        $query->setQuery(
            new ScalarOperator\EqNode('active', 1)
        );
        $filters = $this->filterDs->query($query);

        foreach ($filters as $filter) {
            $rqlQueryObject = $this->parseRql($filter['rql']);
            $rqlQueryObject = $this->addTickLimit($rqlQueryObject, $tickId, $step);
            $matches = $this->timelineDs->query($rqlQueryObject);
            if (count($matches)) {
                // call callback
            }
        }
    }
}