<?php

namespace Korshunov\TimeInterval;

use DateInterval;
use Exception;

/**
 * Class TimeIntervalInterface.
 */
interface TimeIntervalInterface
{
    // Seconds per unit of time ratio
    public const DAY = 86400;
    public const HOUR = 3600;
    public const MINUTE = 60;
    public const SECOND = 1;

    /**
     * Create from "datestring" format.
     *
     * @see https://php.net/manual/en/dateinterval.createfromdatestring.php
     *
     * @param string $time Date string
     *
     * @return TimeIntervalInterface
     */
    public static function createFromDateString(string $time): TimeIntervalInterface;

    /**
     * Create from HMS string [-]h:m[:s].
     *
     * Example: TimeIntervalInterface::createFromHMS('01:12:12');
     *
     * @param string $value String [-]h:m[:s]
     *
     * @return TimeIntervalInterface
     */
    public static function createFromHMS(string $value): TimeIntervalInterface;

    /**
     * Create php DateInterval by hours, minutes and seconds value.
     *
     * @return DateInterval
     *
     * @throws Exception DateInterval constructor exception
     */
    public function createDateInterval(): DateInterval;

    /**
     * Modify time by value and measure(hour, minute, second).
     *
     * @param int $value   Value
     * @param int $measure Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     *
     * @return TimeIntervalInterface
     */
    public function modify(int $value, int $measure): TimeIntervalInterface;

    /**
     * Add TimeIntervalInterface object to current object.
     * Signed, adding positive, subtracting negative.
     *
     * @param TimeIntervalInterface $time TimeIntervalInterface Object
     *
     * @return TimeIntervalInterface
     */
    public function add(TimeIntervalInterface $time): TimeIntervalInterface;

    /**
     * Subtract object TimeInterval from current,
     * Signed, subtracting positive, adding negative.
     *
     * @param TimeIntervalInterface $time TimeIntervalInterface Object
     *
     * @return TimeIntervalInterface
     */
    public function sub(TimeIntervalInterface $time): TimeIntervalInterface;

    /**
     * Amount of time to hours.
     *
     * @param int $precision Rounding precision. Default = 0.
     * @param int $mode      Rounding mode. Default = PHP_ROUND_HALF_UP.
     *
     * @return float
     */
    public function convertToHours(int $precision = 0, $mode = PHP_ROUND_HALF_UP): float;

    /**
     * Amount of time to minutes.
     *
     * @param int $precision Rounding precision. Default = 0.
     * @param int $mode      Rounding mode. Default = PHP_ROUND_HALF_UP.
     *
     * @return float
     */
    public function convertToMinutes(int $precision = 0, $mode = PHP_ROUND_HALF_UP): float;

    /**
     * Amount of time to seconds.
     *
     * @param int $precision Rounding precision. Default = 0.
     * @param int $mode      Rounding mode. Default = PHP_ROUND_HALF_UP.
     *
     * @return float
     */
    public function convertToSeconds(int $precision = 0, $mode = PHP_ROUND_HALF_UP): float;

    /**
     * Whole hours in the current interval, for the format h:m:s.
     */
    public function getHours(): int;

    /**
     * Minutes remaining after hours in the current interval, for the format h:m:s.
     */
    public function getMinutes(): int;

    /**
     * Seconds remaining after hours and minutes in the current interval, for the format h:m:s.
     */
    public function getSeconds(): int;

    /**
     * Formatting, similar to \DateInterval::format() time formatting.
     *
     * @see https://php.net/manual/en/dateinterval.format.php
     *
     * @param string $format Format
     *
     * @return string
     *
     * @throws Exception
     */
    public function format(string $format): string;
}
