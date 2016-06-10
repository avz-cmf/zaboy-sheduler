<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;

/**
 * Class Callback\StaticMethod
 *
 * This class implements an abstraction of callback - static method of class
 *
 * @see \zaboy\scheduler\Callback\Factory\StatichMethodAbstractFactory
 * @package zaboy\scheduler\Callback
 */
class StaticMethod implements CallbackInterface
{
    protected $method;

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __construct(array $params = [])
    {
        if (!isset($params['method'])) {
            throw new CallbackException("The necessary parameter \"function\" is expected");
        }
        $realMethodName = join('::', (array) $params['method']);
        if (!is_callable($params['method'])) {
            throw new CallbackException("The specified method \"{$realMethodName}\" is not callable or it's not static");
        }
        $this->method = $params['method'];
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function call(array $options = [])
    {
        return call_user_func($this->method, $options);
    }
}