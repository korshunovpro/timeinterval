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
     * @param int $timeUnit Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     *
     * @return TimeIntervalInterface
     */
    public function modify(int $value, int $timeUnit = self::SECOND): TimeIntervalInterface
    {
        $this->exceptionIfUnitNotExists($timeUnit);

        return new static($this->seconds + ($value * self::SECOND_PER_UNIT[$timeUnit]));
    }
}
