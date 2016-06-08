<?php

namespace zaboy\scheduler\Ticker\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use zaboy\scheduler\Ticker\Ticker;

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