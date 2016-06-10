<?php

namespace zaboy\scheduler\Callback\Factory;

use zaboy\scheduler\Callback\Script;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * The abstract factory for all types of callbaks
 *
 * Class AbstractFactoryAbstract
 * @package zaboy\scheduler\Callback\Factory
 */
abstract class AbstractFactoryAbstract implements AbstractFactoryInterface
{
    const CLASS_IS_A = '';

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config')['callback'];
        if (!isset($config[$requestedName]['class'])) {
            return false;
        }
        $requestedClassName = $config[$requestedName]['class'];
        return is_a($requestedClassName, static::CLASS_IS_A, true);
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config')['callback'];
        $serviceConfig = $config[$requestedName];
        $requestedClassName = $serviceConfig['class'];
        $params = $serviceConfig['params'];
        return new $requestedClassName($params);
    }
}