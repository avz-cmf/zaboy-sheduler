<?php

// TODO: This code won't be use some time
/*if (substr(PHP_OS, 0, 3) == "WIN") {
    echo "This script does not work on Windows";
    exit;
}*/

chdir(dirname(__DIR__));
require './vendor/autoload.php';

$container = include './config/container.php';

/** @var zaboy\scheduler\Callback\Script $scriptCallback */
$scriptCallback = $container->get('script_callback');
$scriptCallback->init([
    'script_name' => 'scripts/tick.php'
]);
$scriptCallback->call();