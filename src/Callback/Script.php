<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;

/**
 * Class Callback\Script
 *
 * This class implements an abstraction of callback - php-script
 * It can parse parameters from command line
 * @package zaboy\scheduler\Callback
 */
class Script implements CallbackInterface
{
    const PARAMETERS_PREFIX = '-';

    protected $script = null;

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function init(array $initParams = [])
    {
        if (!isset($initParams['script_name'])) {
            throw new CallbackException("The necessary parameter \"script_name\" is expected");
        }

        $script = $initParams['script_name'];
        unset($initParams['script_name']);
        if (is_file($script)) {
            $this->script = $script;
        } else {
            $filename = getcwd() . DIRECTORY_SEPARATOR . $script;
            if (!is_file($filename)) {
                throw new CallbackException("Specified script \"$script\" does not exist in path \"" . getcwd() . "\"");
            }
            $this->script = $filename;
        }
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function call(array $dynamicParams = [])
    {
        if (is_null($this->script)) {
            throw new CallbackException("The object was not initialized. Use the method \"init\" at first.");
        }
        $cmd = "php " . $this->script;
        $cmd .= self::unParseParameters($dynamicParams);
        pclose(
            popen($cmd . " &", 'w')
        );
    }

    /**
     * Parse parameters from array (usually command line of scripts)
     *
     * @param $argv
     * @return array
     * @throws \zaboy\scheduler\Callback\CallbackException
     */
    public static function parseCommandLineParameters($argv)
    {
        // find first parameter
        while(count($argv)) {
            $token = $argv[0];
            if (substr_compare($token, self::PARAMETERS_PREFIX, 0, strlen(self::PARAMETERS_PREFIX)) == 0) {
                break;
            }
            array_shift($argv);
        }
        // if count of rest elements is not even - error
        if ((count($argv) % 2) != 0) {
            throw new CallbackException("Wrong parameters count in command line");
        }
        // parse options
        $options = [];
        for ($i = 0; $i < count($argv); $i += 2) {
            $key = substr($argv[$i], strlen(self::PARAMETERS_PREFIX));
            $value = $argv[$i + 1];
            $options[$key] = $value;
        }
        return $options;
    }

    /**
     * Join all parameters from $options to string for passing them via command line
     *
     * @param $options
     * @return string
     */
    public static function unParseParameters($options)
    {
        $cmd = '';
        foreach ($options as $key => $value) {
            $cmd .= ' ' . self::PARAMETERS_PREFIX . $key . ' ' . $value;
        }
        return $cmd;
    }
}