<?php

return [
    'dataStore' => [
        'tick_log_datastore' => [
            'class' => 'zaboy\rest\DataStore\CsvIntId',
            'filename' => getcwd() . '/logs/tick.log',
            'delimiter' => ';',
        ],
        'hop_log_datastore' => [
            'class' => 'zaboy\rest\DataStore\CsvIntId',
            'filename' => getcwd() . '/logs/hop.log',
            'delimiter' => ';',
        ],
    ],
    'ticker' => [
        'log' => [
            'tick' => [
                'max_log_rows' => 600,
            ],
            'hop' => [
                'max_log_rows' => 600,
            ],
        ]
    ]
];