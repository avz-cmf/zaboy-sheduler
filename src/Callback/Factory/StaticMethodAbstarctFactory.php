<?php

namespace zaboy\scheduler\Callback\Factory;

/**
 * Creates if can and returns an instance of class 'Callback\StaticMethod'
 *
 * For correct work the config must contain part below:
 * <code>
 * 'callback' => [
 *     'real_service_name' => [
 *         'class' => 'zaboy\scheduler\Callback\StaticMethod',
 *         'params' => [
 *             'method' => '\Real\Class\Name\Or\Namespace::RealMethodName',
 *             // OR
 *             'method' => ['\Real\Class\Name\Or\Namespace', 'RealMethodName'],
 *          ],
 *     ],
 * ]
 * </code>
 *
 * Class StaticMethodAbstractFactory
 * @package zaboy\scheduler\Callback\Factory
 */
class StaticMethodAbstarctFactory extends AbstractFactoryAbstract
{
    protected $classIsA = 'zaboy\scheduler\Callback\StaticMethod';
}