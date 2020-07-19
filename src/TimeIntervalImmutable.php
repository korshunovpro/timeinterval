<?php

namespace Korshunov\TimeInterval;

/**
 * Class TimeIntervalImmutable.
 *
 * @package Korshunov\TimeInterval
 */
class TimeIntervalImmutable extends TimeInterval
{
    /**
     * {@inheritdoc}
     *
     * @param int $value    Value
     * @param int $timeUnit Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     *
     * @return TimeIntervalInterface
     */
    public function modify(int $value, int $timeUnit = self::SECOND): TimeIntervalInterface
    {
        $this->exceptionIfUnitNotExist($timeUnit);

        return new static($this->seconds + ($value * self::SECOND_PER_UNIT[$timeUnit]));
    }
}
