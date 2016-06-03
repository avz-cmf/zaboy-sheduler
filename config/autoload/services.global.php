<?php

return [

    'services' => [
        'invokables' => [
        ],
        'factories' => [
        ],
        'abstract_factories' => [
            'zaboy\scheduler\DataStore\Factory\TimelineAbstractFactory',
            'zaboy\rest\DataStore\Factory\CsvAbstractFactory',
            'zaboy\scheduler\Callback\Factory\ScriptAbstractFactory'
        ]
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
