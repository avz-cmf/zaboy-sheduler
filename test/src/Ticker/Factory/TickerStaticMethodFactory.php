<?php

namespace zaboy\test\scheduler\Ticker\Factory;

use zaboy\scheduler\Ticker\Factory\TickerFactory;

class TickerStaticMethodFactory extends TickerFactory
{
    protected $serviceName = 'test_ticker_staticmethod_callback';
}