<?php

chdir(dirname(__DIR__));
require './vendor/autoload.php';

use zaboy\sheduler\Ticker;

$timer = new Ticker( );
$timer->start();
