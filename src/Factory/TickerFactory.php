<?php

namespace zaboy\scheduler\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use zaboy\scheduler\Ticker;
use zaboy\scheduler\Callback\Script;

class TickerFactory implements FactoryInterface
{
    protected $serviceName = 'ticker';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')[$this->serviceName];
        $hopCallback = $serviceLocator->get($config['hop']['callback']);
        $tickCallback = $serviceLocator->get($config['tick']['callback']);

        $ticker = new Ticker($tickCallback, $hopCallback, $config);
        return $ticker;
    }
}