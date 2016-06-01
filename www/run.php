<?php

chdir(dirname(__DIR__));
require './vendor/autoload.php';

use ittech227\ticker\Ticker;

$timer = new Ticker();
$timer->start();