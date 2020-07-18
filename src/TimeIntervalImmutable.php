<?php

namespace Korshunov\TimeInterval;

/**
 * Class TimeIntervalImmutable.
 *
 * @package Korshunov\TimeInterval
 */
class TimeIntervalImmutable extends TimeInterval implements TimeIntervalInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int $value   Value
     * @param int $measure Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     *
     * @return TimeIntervalInterface
     */
    public function modify(int $value, int $measure = self::SECOND): TimeIntervalInterface
    {
        return new static($this->seconds + ($value * $measure));
    }
}
