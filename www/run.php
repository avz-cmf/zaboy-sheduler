<?php

chdir(dirname(__DIR__));
require './vendor/autoload.php';

use zaboy\scheduler\Ticker;

$timer = new Ticker();
$timer->start();