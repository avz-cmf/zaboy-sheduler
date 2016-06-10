<?php

namespace zaboy\scheduler\Callback\Factory;

use Interop\Container\ContainerInterface;

/**
 * The abstract factory for all types of callbaks
 *
 * Class AbstractFactoryAbstract
 * @package zaboy\scheduler\Callback\Factory
 */
abstract class AbstractFactoryAbstract extends \zaboy\rest\AbstractFactoryAbstract
{
    const CLASS_IS_A = '';

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config')['callback'];
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config')['callback'];
        $serviceConfig = $config[$requestedName];
        $requestedClassName = $serviceConfig['class'];
        $params = $serviceConfig['params'];
        return new $requestedClassName($params);
    }
}