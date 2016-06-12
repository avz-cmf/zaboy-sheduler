<?php

namespace zaboy\scheduler\DataStore;


class UTCTime
{
    /**
     * Returns UTC timestamp with or without microseconds and specified precision (if microseconds is needed)
     *
     * @param bool|true $withMicroseconds
     * @param int $precision
     * @return float|int
     */
    public static function getUTCTimestamp($withMicroseconds = true, $precision = 1)
    {
        if ($withMicroseconds) {
            $UTCTimestamp = round(microtime(1) - date('Z'), $precision);
        } else {
            $UTCTimestamp = time() - date('Z');
        }
        return $UTCTimestamp;
    }
}