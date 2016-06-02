<?php

namespace zaboy\scheduler\Callback;

use zaboy\scheduler\Callback\CallbackException;

class Callback
{
    const PARAMETERS_PREFIX = '-';

    protected $script;

    /**
     * Callback constructor.
     */
    public function __construct($script)
    {
        $filename = getcwd() . DIRECTORY_SEPARATOR . $script;
        if (!is_file($filename)) {
            throw new CallbackException("Specified script \"$script\" does not exist in path \"" . getcwd() . "\"");
        }
        $this->script = $filename;
    }

    /**
     * Parse parametres from array (usually command line of scripts)
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
     * Join all paramters from $options to string for run in command line
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

    /**
     * Call callback with options in separate process
     *
     * @param array $options
     */
    public function call($options = [])
    {
        $cmd = "php " . $this->script;
        $cmd .= self::unParseParameters($options);
        pclose(
            popen($cmd . " &", 'w')
        );
    }
}