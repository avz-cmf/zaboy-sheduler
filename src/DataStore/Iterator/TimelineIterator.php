<?php

namespace zaboy\scheduler\DataStore\Iterator;

use zaboy\scheduler\DataStore\TimelineDataStoreException;

class TimelineIterator implements \Iterator
{
    const MAX_NUMBER = 100000;

    protected $callback;

    protected $index = null;

    /**
     * @var integer | float
     */
    protected $from;

    /**
     * @var integer
     */
    protected $number;

    /**
     * @var integer | float
     */
    protected $step;

    public function __construct(callable $callback, $from, $number, $step)
    {
        $this->callback = $callback;
        $this->setStep($step);
        $this->setFrom($from);
        $this->setNumber($number);
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function rewind()
    {
        $this->index = $this->from;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function current()
    {
        // TODO: What about time conversion to winter/summer time?
        return call_user_func($this->callback, $this->index);
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function next()
    {
        $this->index += $this->step;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inherit}
     *
     * {@inherit}
     */
    public function valid()
    {
        if (is_null($this->index)) {
            throw new TimelineDataStoreException("The start index wasn't initialized");
        }
        $currentNumber = round(($this->index - $this->from) / $this->step);
        if ($currentNumber == self::MAX_NUMBER) {
            throw new TimelineDataStoreException("The maximum count of items was reached");
        }
        // If the loop is unlimit and the exception wasn't trown then return true
        if (is_null($this->number)) {
            return true;
        }
        // because we'll be here if the loop isn't unlimit
        $isOutOfLimit = $this->index >= $this->from + $this->number * $this->step;
        return !$isOutOfLimit;
    }

    protected function setStep($step)
    {
        if ($step <= 0) {
            throw new TimelineDataStoreException("Paramter \"step\" must be greater than zero");
        }
        // TODO: if it's needed you can implement the additional checking for this parameter
        $this->step = $step;
    }

    protected function setFrom($from)
    {
        // TODO: if it's needed you can implement the checking for this parameter
        $this->from = $from;
    }

    protected function setNumber($number)
    {
        // TODO: if it's needed you can implement the checking for this parameter
        $this->number = $number;
    }
}