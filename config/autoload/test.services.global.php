<?php

return [

    'services' => [
        'invokables' => [
        ],
        'factories' => [
            'test_ticker_script_callback' => 'zaboy\test\scheduler\Factory\TickerScriptFactory',
            'test_ticker_staticmethod_callback' => 'zaboy\test\scheduler\Factory\TickerStaticMethodFactory',
            'test_scheduler' => 'zaboy\test\scheduler\Factory\SchedulerFactory',
        ],
        'abstract_factories' => [
            'zaboy\rest\DataStore\Factory\MemoryAbstractFactory',
        ]
    ],
];
