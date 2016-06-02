<?php

chdir(dirname(__DIR__));

$serviceName = 'hop_log_datastore';
$columns = [
    'hop_start',
    'ttl',
];
$scriptType = 'hop';

include './scripts/common.php';