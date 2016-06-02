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
        ]
    ]
];
