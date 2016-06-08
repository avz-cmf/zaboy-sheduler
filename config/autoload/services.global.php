<?php

return [

    'services' => [
        'invokables' => [
        ],
        'factories' => [
            'timeline_datastore' => 'zaboy\scheduler\DataStore\Factory\TimelineFactory',
            'ticker' => 'zaboy\scheduler\Ticker\Factory\TickerFactory',
            'scheduler' => 'zaboy\scheduler\Scheduler\Factory\SchedulerFactory',
        ],
        'abstract_factories' => [
            'zaboy\rest\DataStore\Factory\CsvAbstractFactory',
            'zaboy\scheduler\Callback\Factory\ScriptAbstractFactory',
            'zaboy\scheduler\Callback\Factory\StaticMethodAbstarctFactory',
            'zaboy\scheduler\Callback\Factory\InstanceAbstractFactory',
        ]
    ],
];
