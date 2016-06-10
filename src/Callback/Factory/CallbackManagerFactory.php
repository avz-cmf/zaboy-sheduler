<?php

namespace zaboy\scheduler\Callback\Factory;

use Interop\Container\ContainerInterface;
use zaboy\scheduler\FactoryAbstract;
use zaboy\scheduler\Callback\CallbackManager;

class CallbackManagerFactory extends FactoryAbstract
{
    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __invoke(ContainerInterface $container)
    {
        return new CallbackManager($container);
    }
}