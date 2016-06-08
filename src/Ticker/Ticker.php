<?php

namespace zaboy\scheduler\Ticker;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;
use zaboy\scheduler\DataStore\UTCTime;

class Ticker
{
    /**
     * After this time crontab must restart Ticker again (by default)
     */
    const DEFAULT_TOTAL_TIME = 60;

    /**
     * Tick frequence by default in seconds
     */
    const DEFAULT_STEP = 1;

    /**
     * The prefix for command line parameters for passing to the tick_callback
     */
    const TICK_PARAMS_PREFIX = 'tick__';

    /**
     * The prefix for command line parameters for passing to the hop_callback
     */
    const HOP_PARAMS_PREFIX = 'hop__';

    /**
     * Time overhead which will lead to exception. The hop souldn't to work greater over this time  (in percents)
     */
    const DEFAULT_CRITIAL_OVERTIME = 100;

    /**
     * Tick frequency in seconds
     *
     * Minimum tested stable value is 0.1 sec
     * @var float | int
     */
    protected $step;

    /**
     * Total time of ticker working
     *
     * @var int
     */
    protected $total_time;

    /**
     * Time overhead which will lead to exception. The hop souldn't to work greater over this time  (in percents)
     *
     * @var int
     */
    protected $critical_overtime;

    /**
     * How many steps left
     *
     * @var float
     */
    protected $leftSteps;

    /**
     * Tick Id is current timestamp with or without tenth parts of seconds
     *
     * @var float|int
     */
    protected $tickId;

    /**
     * Current hop had started at this time
     *
     * @var float|int
     */
    protected $hopStart;

    /**
     * Callback which called every tick
     *
     * @var \zaboy\scheduler\Callback\Interfaces\CallbackInterface
     */
    protected $tick_callback;

    /**
     * Callback which called in the start
     *
     * @var \zaboy\scheduler\Callback\Interfaces\CallbackInterface
     */
    protected $hop_callback;

    /**
     * Options for passing to tick callback
     *
     * @var array
     */
    protected $tickCallbackParams = [];

    /**
     * Options for passing to hop callback
     *
     * @var array
     */
    protected $hopCallbackParams = [];

    /**
     * Ticker constructor.
     *
     * @param $stepFrequence
     */
    public function __construct(CallbackInterface $tickCallback, CallbackInterface $hopCallback, $options = [])
    {
        $this->tick_callback = $tickCallback;
        $this->hop_callback = $hopCallback;

        $this->setDefaultParameters($options);
        $this->setOptions($options);
        $this->leftSteps = floor($this->total_time / $this->step);
    }

    /**
     * Sets the class properties from options
     *
     * @param $options
     */
    protected function setOptions($options)
    {
        $this->options = array();
        foreach ($options as $key => $value) {
            // If param match pattern 'tick__param1 val1'
            if (substr_compare($key, self::TICK_PARAMS_PREFIX, 0, strlen(self::TICK_PARAMS_PREFIX)) == 0) {
                $tickKey = substr($key, strlen(self::TICK_PARAMS_PREFIX));
                $this->tickCallbackParams[$tickKey] = $value;
            }
            // If param match pattern 'hop__param1 val1'
            if (substr_compare($key, self::HOP_PARAMS_PREFIX, 0, strlen(self::HOP_PARAMS_PREFIX)) == 0) {
                $hopKey = substr($key, strlen(self::HOP_PARAMS_PREFIX));
                $this->hopCallbackParams[$hopKey] = $value;
            }
            // Properties of class
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
            // rest parameters are ignored
        }
    }

    /**
     * @param $options
     */
    protected function setDefaultParameters(&$options)
    {
        if (!isset($options['total_time'])) {
            $options['total_time'] = self::DEFAULT_TOTAL_TIME;
        }
        if (!isset($options['step'])) {
            $options['step'] = self::DEFAULT_STEP;
        }
        if ($options['step'] <= 0) {
            throw new \Exception("The parameter \"step\" must be greater tan zero. \"{$options['step']}\" given.");
        }
        if (!isset($options['critical_overtime'])) {
            $options['critical_overtime'] = self::DEFAULT_CRITIAL_OVERTIME;
        }
    }

    /**
     * Trigger for preparing hop run
     */
    public function preRunHopCallback()
    {
        $this->hopStart = UTCTime::getUTCTimestamp($this->step > 1);
        $this->hop_callback->call(array_merge([
            'hop_start' => $this->hopStart,
            'ttl' => $this->total_time,
        ], $this->hopCallbackParams));
    }

    /**
     * Starts timing
     */
    public function start()
    {
        $this->preRunHopCallback();
        $this->runHop();
    }

    /**
     * Runs hop
     */
    public function runHop()
    {
        $this->tickId = UTCTime::getUTCTimestamp($this->step < 1);
        // we're living the TTL time only
        do {
            $startIterationTime = microtime(1);
            // calling tick callback
            $this->tick_callback->call(array_merge([
                'tick_id' => $this->tickId,
                'step' => $this->step,
            ], $this->tickCallbackParams));
            // Checks runtime of callback; if greater than step - triggers notice
            if ((microtime(1) - $startIterationTime) > $this->step) {
//                trigger_error("The call callback took too much time. The ticker could lose the right tact.");
            }
            $this->leftSteps--;
            $this->tickId += $this->step;

            $this->checkOverheadTime();

            // calc in microseconds
            // How much time left to next tick
            $restIterationTime = $this->step * 1000000 - (round(microtime(1) - $startIterationTime));
            if ($restIterationTime > 0) {
                usleep($restIterationTime);
            } else {
                usleep(1000);
            }
        } while ($this->leftSteps > 0);
    }

    /**
     * Checks if this hop works greater than "total_time + (total_time * overhead_time) / 100"
     *
     * @throws \Exception
     */
    protected function checkOverheadTime()
    {
        $now = UTCTime::getUTCTimestamp($this->step < 1);
        $passedTime = $now - $this->hopStart;
        $totalAllowedTime = $this->total_time * $this-> critical_overtime / 100 + $this->total_time;
        if ($passedTime >= $totalAllowedTime) {
            throw new \Exception("Current hop already works greater than allowed overhead time.");
        }
    }
}