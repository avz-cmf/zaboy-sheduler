<?php

return [
    'test_ticker_script_callback' => [
        'total_time' => 30,
        'step' => 1,
        'hop' => [
            'callback' => 'script_hop_callback',
            'callbackParams' => [
                'max_log_rows' => 600,
            ],
        ],
        'tick' => [
            'callback' => 'script_tick_callback',
            'callbackParams' => [
                'max_log_rows' => 600,
            ],
        ],
    ],

    'test_ticker_staticmethod_callback' => [
        'total_time' => 30,
        'step' => 1,
        'hop' => [
            'callback' => 'staticmethod_hop_callback',
            'callbackParams' => [
                'max_log_rows' => 600,
            ],
        ],
        'tick' => [
            'callback' => 'staticmethod_tick_callback',
            'callbackParams' => [
                'max_log_rows' => 600,
            ],
        ],
    ],

    'callback' => [
        'script_hop_callback' => [
            'class' => 'zaboy\scheduler\Callback\Script',
            'params' => [
                'script_name' => 'scripts/hop.php',
            ],
        ],
        'script_tick_callback' => [
            'class' => 'zaboy\scheduler\Callback\Script',
            'params' => [
                'script_name' => 'scripts/tick.php',
            ],
        ],
        'staticmethod_hop_callback' => [
            'class' => 'zaboy\scheduler\Callback\StaticMethod',
            'params' => [
                'method' => 'zaboy\test\scheduler\TickerStaticMethodCallbackTest::methodForHopCallback',
            ],
        ],
        'staticmethod_tick_callback' => [
            'class' => 'zaboy\scheduler\Callback\StaticMethod',
            'params' => [
                'method' => 'zaboy\test\scheduler\TickerStaticMethodCallbackTest::methodForTickCallback',
            ],
        ]
    ],
];