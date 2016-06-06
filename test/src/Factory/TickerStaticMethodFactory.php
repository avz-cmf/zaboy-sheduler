<?php

namespace zaboy\test\scheduler\Factory;

use zaboy\scheduler\Factory\TickerFactory;

class TickerStaticMethodFactory extends TickerFactory
{
    protected $serviceName = 'test_ticker_staticmethod_callback';
}