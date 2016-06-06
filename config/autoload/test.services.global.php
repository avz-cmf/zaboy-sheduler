<?php

return [

    'services' => [
        'invokables' => [
        ],
        'factories' => [
            'test_ticker_script_callback' => 'zaboy\test\scheduler\Factory\TickerScriptFactory',
            'test_ticker_staticmethod_callback' => 'zaboy\test\scheduler\Factory\TickerStaticMethodFactory',
        ],
        'abstract_factories' => [
        ]
    ],
];
