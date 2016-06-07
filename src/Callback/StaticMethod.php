<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;

class StaticMethod implements CallbackInterface
{
    protected $method;

    public function __construct(array $params = [])
    {
        if (!isset($params['method'])) {
            throw new CallbackException("The necessary parameter \"function\" is expected");
        }
        if (is_array($params['method'])) {
            $params['method'] = join('::', $params['method']);
        }
        if (!is_callable($params['method'])) {
            throw new CallbackException("The specified method \"{$params['method']}\" is not callable");
        }
        $this->method = $params['method'];
    }

    public function call(array $options = [])
    {
        return call_user_func_array($this->method, $options);
    }
}