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
class ScriptAbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config');
        if (!isset($config['callback'][$requestedName]['class'])) {
            return false;
        }
        $requestedClassName = $config['callback'][$requestedName]['class'];
        return is_a($requestedClassName, 'zaboy\scheduler\Callback\Script', true);
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config');
        $serviceConfig = $config['callback'][$requestedName];
        $requestedClassName = $serviceConfig['class'];
        $params = $serviceConfig['params'];
        return new $requestedClassName($params);
    }


}