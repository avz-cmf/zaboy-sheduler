<?php

namespace zaboy\scheduler\Callback\Factory;

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
class ScriptAbstractFactory extends AbstractFactoryAbstract
{
    const CLASS_IS_A = 'zaboy\scheduler\Callback\Script';
}