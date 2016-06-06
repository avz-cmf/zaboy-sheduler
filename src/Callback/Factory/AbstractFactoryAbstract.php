<?php

namespace zaboy\scheduler\Callback\Factory;

use zaboy\scheduler\Callback\Script;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates if can and returns an instance of class 'Callback\Script'
 *
 * For correct work the config must contain part below:
 * <code>
 * 'callback' => [
 *     'real_service_name' => [
 *         'class' => 'zaboy\scheduler\Callback\Script',
 *         'params' => [
 *             'script_name' => 'real/script/name.php',
 *          ],
 *     ],
 * ]
 * </code>
 *
 * Class ScriptAbstractFactory
 * @package zaboy\scheduler\Callback\Factory
 */
abstract class AbstractFactoryAbstract implements AbstractFactoryInterface
{
    protected $classIsA;
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
        return is_a($requestedClassName, $this->classIsA, true);
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