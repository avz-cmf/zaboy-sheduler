<?php

namespace zaboy\test\scheduler\Ticker;

use zaboy\test\sheduler\Ticker\TickerAbstractTest;
use \Xiag\Rql\Parser\Node\LimitNode;
use \Xiag\Rql\Parser\Node\SortNode;
use \Xiag\Rql\Parser\Node\SelectNode;

class TickerStaticMethodCallbackTest extends TickerAbstractTest
{
    protected $tickerServiceName = 'test_ticker_staticmethod_callback';

    protected static $logItemDataColumns;

    protected static $callbackType;

    protected static $logServiceName;

    public static function methodForHopCallback($options)
    {
        self::$logItemDataColumns = [
            'hop_start',
            'ttl',
        ];
        self::$logServiceName = 'hop_log_datastore';
        self::$callbackType = 'hop';

        self::callbackCommon($options);
    }

    public static function methodForTickCallback($options)
    {
        self::$logItemDataColumns = [
            'tick_id',
            'step',
        ];
        self::$logServiceName = 'tick_log_datastore';
        self::$callbackType = 'tick';
        self::callbackCommon($options);
    }

    protected static function callbackCommon($options)
    {
        /** @var \Zend\ServiceManager\ServiceManager $container */
        $container = include './config/container.php';
        if (!$container->has(self::$logServiceName)) {
            throw new \Exception("The service \"" . self::$logServiceName . "\" must be specified in config/datastore");
        }
        $log = $container->get(self::$logServiceName);

        // Writes to log
        $itemData = array_flip(self::$logItemDataColumns);
        array_walk($itemData, function (&$item, $key) use ($options) {
            if (!isset($options[$key])) {
                throw new \Exception("Expected necessary parameter \"{$key}\"");
            }
            $item = $options[$key];
        });
        $log->create($itemData);

        // Clears old records in the log
        if (isset($options['max_log_rows'])) {
            $maxLogRows = $options['max_log_rows'];
        } else {
            $config = $container->get('config')['test_ticker_staticmethod_callback'][self::$callbackType]['callbackParams'];
            $maxLogRows = $config['max_log_rows'];
        }

        $query = new \Xiag\Rql\Parser\Query();
        $query->setSelect(new SelectNode(['id']));
        $query->setLimit(new LimitNode($maxLogRows, $maxLogRows));
        $query->setSort(new SortNode(['id' => '-1']));

        $rowsForDeleting = $log->query($query);
        foreach ($rowsForDeleting as $row) {
            $log->delete($row['id']);
        }
    }

}