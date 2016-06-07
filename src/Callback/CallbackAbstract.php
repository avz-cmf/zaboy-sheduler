<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;

abstract class CallbackAbstract
{
    /** @var \Zend\ServiceManager\ServiceManager $serviceManager */
    protected static $serviceManager = null;

    protected static function getServiceManager()
    {
        if (is_null(self::$serviceManager)) {
            self::$serviceManager = include getcwd() . '/config/container.php';
        }
        return self::$serviceManager;
    }

    public static function factory($serviceName)
    {
        $instance = self::getServiceManager()->get($serviceName);
        if (!$instance instanceof CallbackInterface) {
            throw new CallbackException("The instance of specified service is not Callback");
        }
        return $instance;
    }
}