<?php

chdir(dirname(__DIR__));
require './vendor/autoload.php';

$container = include './config/container.php';
$ticker = $container->get('ticker');
$ticker->start();