<?php

namespace Korshunov\TimeInterval;

use DateInterval;
use Exception;
use InvalidArgumentException;

/**
 * Class TimeInterval.
 */
class TimeInterval implements TimeIntervalInterface
{
    /** @var int Amount of seconds */
    protected $seconds;

    /**
     * TimeInterval constructor.
     *
     * @param int $seconds Amount of seconds
     */
    public function __construct(int $seconds = 0)
    {
        $this->seconds = $seconds;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $time Date string
     *
     * @throws InvalidArgumentException
     */
    public static function createFromDateString(string $dateString): TimeIntervalInterface
    {
        $interval = DateInterval::createFromDateString($dateString);

        if (!empty($interval->y) || !empty($interval->m)) {
            throw new InvalidArgumentException(
                'Wrong format, expected only values of days, hours, minutes and seconds'
            );
        }

        $seconds = $interval->d * self::DAY
            + $interval->h * self::HOUR
            + $interval->m * self::MINUTE
            + $interval->s
            * ($interval->invert ? -1 : 1);

        return new static($seconds);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $value String [-]h:m[:s]
     */
    public static function createFromHMS(string $value): TimeIntervalInterface
    {
        $value = trim($value);

        if (!preg_match('#^[-+]?\d+:\d+(:\d+)?$#is', $value)) {
            throw new InvalidArgumentException('Wrong format, expected [-]h:m[:s]');
        }

        $sign = 1;
        if (0 === stripos($value, '-')) {
            $sign = -1;
        }

        $value = explode(':', $value);
        $hours = (int) $value[0] * self::HOUR;
        $minutes = (int) $value[1] * self::MINUTE;
        $seconds = !empty($value[2]) ? abs((int) $value[2]) : 0;

        $seconds = $sign * (abs($hours) + $minutes + $seconds);

        return new static($seconds);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception DateInterval constructor exception
     */
    public function createDateInterval(): DateInterval
    {
        $str[] = abs($this->getHours()) . 'H';
        $str[] = abs($this->getMinutes()) . 'M';
        $str[] = abs($this->getSeconds()) . 'S';

        $interval = new DateInterval('PT' . implode('', $str));
        $interval->invert = (int) $this->seconds < 0;

        return $interval;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $value   Value
     * @param int $measure Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     *
     * @return $this
     */
    public function modify(int $value, int $measure = self::SECOND): TimeIntervalInterface
    {
        $this->seconds += $value * $measure;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param TimeIntervalInterface $time TimeIntervalInterface Object
     *
     * @return $this
     */
    public function add(TimeIntervalInterface $time): TimeIntervalInterface
    {
        return $this->modify($time->convertToSeconds());
    }

    /**
     * {@inheritdoc}
     *
     * @param TimeIntervalInterface $time TimeIntervalInterface Object
     *
     * @return $this
     */
    public function sub(TimeIntervalInterface $time): TimeIntervalInterface
    {
        return $this->modify(-1 * $time->convertToSeconds());
    }

    /**
     * {@inheritdoc}
     *
     * @param int $precision Rounding precision. Default = 0.
     * @param int $mode      Rounding mode. Default = PHP_ROUND_HALF_UP.
     *
     * @return float
     */
    public function convertToHours(int $precision = 0, $mode = PHP_ROUND_HALF_UP): float
    {
        if ($precision >= 0) {
            return round($this->seconds / self::HOUR, $precision, $mode);
        }

        return (int) ($this->seconds / self::HOUR);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $precision Rounding precision. Default = 0.
     * @param int $mode      Rounding mode. Default = PHP_ROUND_HALF_UP.
     *
     * @return float
     */
    public function convertToMinutes(int $precision = 0, $mode = PHP_ROUND_HALF_UP): float
    {
        if ($precision >= 0) {
            return round($this->seconds / self::MINUTE, $precision, $mode);
        }

        return (int) ($this->seconds / self::MINUTE);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $precision Rounding precision. Default = 0.
     * @param int $mode      Rounding mode. Default = PHP_ROUND_HALF_UP.
     *
     * @return float
     */
    public function convertToSeconds(int $precision = 0, $mode = PHP_ROUND_HALF_UP): float
    {
        if ($precision >= 0) {
            return round($this->seconds, $precision, $mode);
        }

        return (int) $this->seconds;
    }

    /**
     * {@inheritdoc}
     */
    public function getHours(): int
    {
        return (int) ($this->seconds / self::HOUR);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinutes(): int
    {
        return (int) ($this->seconds % self::HOUR / self::MINUTE);
    }

    /**
     * {@inheritdoc}
     */
    public function getSeconds(): int
    {
        return (int) ($this->seconds % self::HOUR % self::MINUTE);
    }

    /**
     * {@inheritdoc}
     *
     * Additional placeholders:
     * %x - amount of whole minutes
     * %X - amount of whole minutes with leading zero.
     *
     * @param string $format Format
     *
     * @return string
     *
     * @throws Exception
     */
    public function format(string $format): string
    {
        return $this->additionalFormat(
            $this->createDateInterval()->format($format)
        );
    }

    /**
     * Additional formatting.
     *
     * @param string $format Format
     *
     * @return string
     */
    protected function additionalFormat(string $format): string
    {
        return str_replace(
            array_keys($this->additionalFormatRules()),
            array_values($this->additionalFormatRules()),
            $format
        );
    }

    /**
     * Extends format placeholders.
     *
     * Placeholders:
     * %x - amount of whole minutes
     * %X - amount of whole minutes with leading zero.
     *
     * @return array
     */
    protected function additionalFormatRules(): array
    {
        return [
            '%x' => abs($this->convertToMinutes()),
            '%X' => sprintf('%02d', abs($this->convertToMinutes())),
        ];
    }
}
