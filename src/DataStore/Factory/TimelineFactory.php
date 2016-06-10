<?php

namespace zaboy\scheduler\DataStore\Factory;

use Interop\Container\ContainerInterface;
use zaboy\scheduler\FactoryAbstract;
use zaboy\scheduler\DataStore\Timeline;

class TimelineFactory extends FactoryAbstract
{
    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function __invoke(ContainerInterface $container)
    {
        return new Timeline();
    }
}