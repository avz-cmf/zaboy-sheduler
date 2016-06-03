<?php

return [
    'dataStore' => [
        'tick_log_datastore' => [
            'class' => 'zaboy\rest\DataStore\CsvIntId',
            'filename' => getcwd() . '/test/logs/tick.log',
            'delimiter' => ';',
        ],
        'hop_log_datastore' => [
            'class' => 'zaboy\rest\DataStore\CsvIntId',
            'filename' => getcwd() . '/test/logs/hop.log',
            'delimiter' => ';',
        ],
    ],
    'ticker' => [
        'tick' => [
            'callbackParams' => [
                'max_log_rows' => 600,
            ],
        ],
        'hop' => [
            'callbackParams' => [
                'max_log_rows' => 600,
            ],
        ],
        'total_time' => 30,
        'step' => 1
    ],
    'callback' => [
        'hop_callback' => [
            'class' => 'zaboy\scheduler\Callback\Script',
            'params' => [
                'script_name' => 'scripts/hop.php',
            ],
        ],
        'tick_callback' => [
            'class' => 'zaboy\scheduler\Callback\Script',
            'params' => [
                'script_name' => 'scripts/tick.php',
            ],
        ]
    ],
];