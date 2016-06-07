<?php

namespace zaboy\scheduler\Callback\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates if can and returns an instance of class 'Callback\Instance'
 *
 * For correct work the config must contain part below:
 * <code>
 * 'callback' => [
 *     'real_service_name_for_this_callback_type' => [
 *         'class' => 'zaboy\scheduler\Callback\Instance',
 *         'params' => [
 *             'instanceServiceName' => 'service_name_for_instance_which_method_will_be_called',
 *             'instanceMethodName' => 'real_method_name_which_wil_be_called',
 *          ],
 *     ],
 * ]
 * </code>
 *
 * Class ScriptAbstractFactory
 * @package zaboy\scheduler\Callback\Factory
 */
class InstanceAbstractFactory extends AbstractFactoryAbstract
{
    protected $classIsA = 'zaboy\scheduler\Callback\Instance';

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config')['callback'];
        $serviceConfig = $config[$requestedName];
        // Class of callback object, will be 'zaboy\scheduler\Callback\Instance'
        $requestedClassName = $serviceConfig['class'];
        // The first parameter which the callback object gets is instance which method it calls
        $dependencyInstance = $serviceLocator->get($serviceConfig['params']['instanceServiceName']);
        // Second parameter is name of method for call
        $methodName = $serviceConfig['params']['instanceMethodName'];

        $instance = new $requestedClassName([
            'instance' => $dependencyInstance,
            'method' => $methodName,
        ]);
        return $instance;
    }


}