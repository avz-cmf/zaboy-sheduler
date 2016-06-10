<?php

chdir(getcwd());
require './vendor/autoload.php';

use \zaboy\scheduler\Callback\Script;

$options = Script::getCallOptions($_SERVER['argv']);

$serviceName = 'tick_log_datastore';

/** @var Zend\ServiceManager\ServiceManager $container */
$container = include './config/container.php';
if (!$container->has($serviceName)) {
    throw new Exception("The service \"{$serviceName}\" must be specified in config/datastore");
}
$log = $container->get('tick_log_datastore');

$itemData = [
    'tick_id' => \zaboy\scheduler\DataStore\UTCTime::getUTCTimestamp(),
    'step' => print_r($options, 1),
];
$log->create($itemData);