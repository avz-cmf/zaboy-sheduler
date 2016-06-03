<?php

chdir(dirname(__DIR__));
require './vendor/autoload.php';

use zaboy\scheduler\Ticker;
use zaboy\scheduler\Callback\Script;

$container = include './config/container.php';
$tickCallback = $container->get('tick_callback');
$hopCallback = $container->get('hop_callback');

$config = $container->get('config')['ticker'];
$commandLineOptions = Script::parseCommandLineParameters($_SERVER['argv']);
// Command line options have higher priority
$options = array_merge($config, $commandLineOptions);

$timer = new Ticker($tickCallback, $hopCallback, $options);
$timer->start();