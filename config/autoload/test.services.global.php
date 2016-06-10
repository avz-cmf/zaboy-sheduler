<?php

return [

    'services' => [
        'invokables' => [
        ],
        'factories' => [
            'test_ticker_script_callback' => 'zaboy\test\scheduler\Ticker\Factory\TickerScriptFactory',
            'test_ticker_staticmethod_callback' => 'zaboy\test\scheduler\Ticker\Factory\TickerStaticMethodFactory',
            'test_scheduler' => 'zaboy\scheduler\Scheduler\Factory\SchedulerFactory',
        ],
        'abstract_factories' => [
            'zaboy\rest\DataStore\Factory\MemoryAbstractFactory',
        ]
    ],
];
