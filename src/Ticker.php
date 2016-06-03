<?php

namespace zaboy\scheduler;

use zaboy\scheduler\Callback\Interfaces\CallbackInterface;

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
     * Callback which called every tick
     *
     * @var \zaboy\scheduler\Callback\Interfaces
     */
    protected $tick_callback;

    /**
     * Callback which called in the start
     *
     * @var \zaboy\scheduler\Callback\Interfaces
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
    }

    /**
     * Returns id of hop/tick
     *
     * @return float|int
     */
    public function getTickId()
    {
        if ($this->step < 1) {
            $id = round(microtime(1), 1);
        } else {
            $id = time();
        }
        return $id;
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
    }

    /**
     * Trigger for preparing hop run
     */
    public function preRunHopCallback()
    {
        $this->hop_callback->call(array_merge([
            'hop_start' => $this->getTickId(),
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
        $totalTime = $this->total_time;
        // we're living the TTL time only
        do {
            $startIterationTime = microtime(1);
            // calling tick callback
            $this->tick_callback->call(array_merge([
                'tick_id' => $this->getTickId(),
                'step' => $this->step,
            ], $this->tickCallbackParams));
            // Checks runtime of callback; if greater than step - triggers notice
            if ((microtime(1) - $startIterationTime) > $this->step) {
                trigger_error("The call callback took too much time. The ticker could lose the right tact.");
            }
            $totalTime -= $this->step;
            // calc in microseconds
            // How much time left to next tick
            $restIterationTime = $this->step * 1000000 - (round(microtime(1) - $startIterationTime));
            if ($restIterationTime > 0) {
                usleep($restIterationTime);
            } else {
                usleep(1000);
            }
        } while ($totalTime > 0);
    }
}