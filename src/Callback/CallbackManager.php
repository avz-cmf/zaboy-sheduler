<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

class CallbackManager implements ServiceLocatorInterface
{
    /** @var  \Zend\ServiceManager\ServiceManager $serviceManager */
    protected $serviceManager;

    /**
     * CallbackManager constructor.
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new CallbackException("The specified service with name \"{$name}\" does not exist");
        }
        $instance = $this->serviceManager->get($name);
        if (!$instance instanceof CallbackInterface) {
            throw new CallbackException("The instance of specified service is not Callback");
        }
        return $instance;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function has($name)
    {
        $config = $this->serviceManager->get('config');
        return (isset($config['callback'][$name]));
    }
}