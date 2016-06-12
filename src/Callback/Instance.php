<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;

/**
 * Class Callback\Instance
 *
 * Implements an abstraction of callback - object instance and its method (not static)
 *
 * @see \zaboy\scheduler\Callback\Factory\InstanceAbstractFactory
 * @package zaboy\scheduler\Callback
 */
class Instance implements CallbackInterface
{
    /**
     * Instance whose method will be called
     *
     * @var object
     */
    protected $instance;

    /**
     * Method name
     *
     * @var string
     */
    protected $method;

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __construct(array $params = [])
    {
        if (!isset($params['instance']) || !isset($params['method'])) {
            throw new CallbackException("The necessary parameters \"instance\" and \"method\" are expected");
        }
        // Instance must be an object!!
        $instanceType = gettype($params['instance']);
        if ($instanceType != 'object') {
            throw new CallbackException("The parameter \"instance\" must be an object. \"{$instanceType}\" given.");
        }
        $this->instance = $params['instance'];
        // Cheks if the specified method exists in instance
        $methodName = $params['method'];
        if (!method_exists($this->instance, $methodName)) {
            throw new CallbackException("Specified method \"{$methodName}\" does not exist in class \"" . get_class($this->instance) . "\"");
        }
        $this->method = $methodName;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function call(array $options = [])
    {
        return call_user_func_array([$this->instance, $this->method], $options);
    }

}