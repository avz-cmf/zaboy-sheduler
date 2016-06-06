<?php

namespace zaboy\test\scheduler\Factory;

use zaboy\scheduler\Factory\TickerFactory;

class TickerScriptFactory extends TickerFactory
{
    protected $serviceName = 'test_ticker_script_callback';
}