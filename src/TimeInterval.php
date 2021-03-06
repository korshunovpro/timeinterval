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
    // Second per units of time ratio
    protected const SECOND_PER_UNIT = [
        self::DAY => 86400,
        self::HOUR => 3600,
        self::MINUTE => 60,
        self::SECOND => 1,
    ];

    /** @var int Amount of seconds */
    protected $seconds;

    /**
     * TimeInterval constructor.
     *
     * @param int $value    Value
     * @param int $timeUnit Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     */
    public function __construct(int $value = 0, $timeUnit = self::SECOND)
    {
        $this->exceptionIfUnitNotExist($timeUnit);

        $this->seconds = $value * self::SECOND_PER_UNIT[$timeUnit];
    }

    /**
     * {@inheritdoc}
     *
     * @param string $dateString Date string
     *
     * @throws InvalidArgumentException
     */
    public static function createFromDateString(string $dateString): TimeIntervalInterface
    {
        $interval = DateInterval::createFromDateString($dateString);

        return new static(static::intervalToSeconds($interval));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $intervalSpec Interval spec string
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function createFromIntervalSpec(string $intervalSpec): TimeIntervalInterface
    {
        $interval = new DateInterval($intervalSpec);

        return new static(static::intervalToSeconds($interval));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $string String [-]h:m[:s]
     */
    public static function createFromHMS(string $string): TimeIntervalInterface
    {
        if (!preg_match('#^[-+]?\d+:\d+(:\d+)?$#is', $string)) {
            throw new InvalidArgumentException('Wrong format, expected [-]h:m[:s]');
        }

        (0 === strpos($string, '-')) ? $sign = -1 : $sign = 1;

        $value = explode(':', $string);

        $hours = (int) $value[0] * self::SECOND_PER_UNIT[self::HOUR];
        $minutes = (int) $value[1] * self::SECOND_PER_UNIT[self::MINUTE];
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
     * @param int $value    Value
     * @param int $timeUnit Unit of time, TimeIntervalInterface::HOUR[MINUTE|SECOND]
     *                     default: SECOND
     *
     * @return $this
     */
    public function modify(int $value, int $timeUnit = self::SECOND): TimeIntervalInterface
    {
        $this->exceptionIfUnitNotExist($timeUnit);

        $this->seconds += $value * self::SECOND_PER_UNIT[$timeUnit];

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
        return $this->convert(self::HOUR, $precision, $mode);
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
        return $this->convert(self::MINUTE, $precision, $mode);
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
        return $this->convert(self::SECOND, $precision, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getHours(): int
    {
        return (int) ($this->seconds / self::SECOND_PER_UNIT[self::HOUR]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinutes(): int
    {
        return (int) ($this->seconds % self::SECOND_PER_UNIT[self::HOUR] / self::SECOND_PER_UNIT[self::MINUTE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSeconds(): int
    {
        return (int) ($this->seconds % self::SECOND_PER_UNIT[self::HOUR] % self::SECOND_PER_UNIT[self::MINUTE]);
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

    /**
     * Convert to other unit.
     *
     * @param int $timeUnit
     * @param int $precision
     * @param int $mode
     *
     * @return float
     */
    protected function convert(int $timeUnit, int $precision = 0, $mode = PHP_ROUND_HALF_UP): float
    {
        $this->exceptionIfUnitNotExist($timeUnit);

        return round(
            $this->seconds / self::SECOND_PER_UNIT[$timeUnit],
            $precision,
            $mode
        );
    }

    /**
     * Throw exception if unit is not Exist
     *
     * @param int $unit Time unit key(TimeIntervalInterface::SECOND[DAY|HOUR|MINUTE])
     */
    protected function exceptionIfUnitNotExist(int $unit): void
    {
        if (!isset(self::SECOND_PER_UNIT[$unit])) {
            throw new InvalidArgumentException(
                'Unit is not Exist, TimeIntervalInterface::SECOND[DAY|HOUR|MINUTE] expected'
            );
        }
    }

    /**
     * Convert DateInterval to seconds.
     *
     * @param DateInterval $interval
     *
     * @return int
     */
    protected static function intervalToSeconds(DateInterval $interval): int
    {
        if (!empty($interval->y) || !empty($interval->m)) {
            throw new InvalidArgumentException(
                'Wrong format, expected only values of days, hours, minutes and seconds'
            );
        }

        return $interval->d * self::SECOND_PER_UNIT[self::DAY]
            + $interval->h * self::SECOND_PER_UNIT[self::HOUR]
            + $interval->i * self::SECOND_PER_UNIT[self::MINUTE]
            + $interval->s
            * ($interval->invert ? -1 : 1);
    }
}
