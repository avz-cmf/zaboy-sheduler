<?php

chdir(dirname(__DIR__));

$serviceName = 'tick_log_datastore';
$columns = [
    'tick_id',
    'step',
];
$scriptType = 'tick';

include './scripts/common.php';
