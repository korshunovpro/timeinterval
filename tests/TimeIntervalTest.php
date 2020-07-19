<?php

namespace Korshunov\TimeInterval\Tests;

use Korshunov\TimeInterval\TimeInterval;
use Korshunov\TimeInterval\TimeIntervalInterface;
use PHPUnit\Framework\TestCase;
use DateInterval;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

/**
 * Class TimeIntervalTest.
 *
 * @coversDefaultClass TimeInterval
 */
class TimeIntervalTest extends TestCase
{
    // Constants
    protected const SECOND_PER_DAY = 86400;
    protected const SECOND_PER_HOUR = 3600;
    protected const SECOND_PER_MINUTE = 60;

    protected const FAKE_WRONG_TIME_UNIT = 999999999;

    /**
     * Test instance init value.
     */
    public function testInstance()
    {
        $time1 = new TimeInterval(self::SECOND_PER_HOUR);
        $time2 = new TimeInterval(self::SECOND_PER_HOUR, TimeIntervalInterface::SECOND);
        $time3 = new TimeInterval(1, TimeIntervalInterface::HOUR);
        $time4 = new TimeInterval(-1 * 1, TimeIntervalInterface::HOUR);

        self::assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
        self::assertEquals(self::SECOND_PER_HOUR, $time2->convertToSeconds());
        self::assertEquals(self::SECOND_PER_HOUR, $time3->convertToSeconds());
        self::assertEquals(-1 * self::SECOND_PER_HOUR, $time4->convertToSeconds());

        $this->expectException(InvalidArgumentException::class);
        new TimeInterval(self::SECOND_PER_HOUR, self::FAKE_WRONG_TIME_UNIT);

        $float = 5.156;
        $timeFloat = new TimeInterval($float);
        self::assertEquals((int) $float, $timeFloat->convertToSeconds());
    }

    /**
     * @covers TimeInterval::modify
     */
    public function testModify()
    {
        $time = new TimeInterval(self::SECOND_PER_HOUR);

        $time->modify(55);
        self::assertEquals(55 + self::SECOND_PER_HOUR, $time->convertToSeconds());

        $time->modify(-55);
        self::assertEquals(self::SECOND_PER_HOUR, $time->convertToSeconds());

        $time->modify(1, TimeIntervalInterface::HOUR);
        self::assertEquals(2 * self::SECOND_PER_HOUR, $time->convertToSeconds());

        $time->modify(-1, TimeIntervalInterface::HOUR);
        self::assertEquals(self::SECOND_PER_HOUR, $time->convertToSeconds());
    }

    /**
     * @covers TimeInterval::add
     */
    public function testAdd()
    {
        $time1 = new TimeInterval(self::SECOND_PER_HOUR);
        $time2 = new TimeInterval(self::SECOND_PER_HOUR);
        $time3Negative = new TimeInterval(-1 * self::SECOND_PER_HOUR);

        $time1->add($time2);
        self::assertEquals(2 * self::SECOND_PER_HOUR, $time1->convertToSeconds());

        $time1->add($time3Negative);
        self::assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
    }

    /**
     * @covers TimeInterval::sub
     */
    public function testSub()
    {
        $time1 = new TimeInterval(self::SECOND_PER_HOUR);
        $time2 = new TimeInterval(self::SECOND_PER_HOUR);
        $time3Negative = new TimeInterval(-1 * self::SECOND_PER_HOUR);

        $time1->sub($time2);
        self::assertEquals(0, $time1->convertToSeconds());

        $time1->sub($time3Negative);
        self::assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
    }

    /**
     * @covers TimeInterval::convertToHours
     */
    public function testConvertToHours()
    {
        $time = new TimeInterval(self::SECOND_PER_HOUR);
        self::assertEquals(
            1,
            $time->convertToHours()
        );

        $time = new TimeInterval(self::SECOND_PER_HOUR * 1.8);
        self::assertEquals(
            1.8,
            $time->convertToHours(1)
        );

        $time = new TimeInterval(self::SECOND_PER_HOUR * 1.85);
        self::assertEquals(
            1.9,
            $time->convertToHours(1)
        );

        $time = new TimeInterval(self::SECOND_PER_HOUR * 1.85);
        self::assertEquals(
            1.85,
            $time->convertToHours(2)
        );
    }

    /**
     * @covers TimeInterval::convertToMinutes
     */
    public function testConvertToMinutes()
    {
        $time = new TimeInterval(self::SECOND_PER_MINUTE);
        self::assertEquals(
            1,
            $time->convertToMinutes()
        );

        $time = new TimeInterval(self::SECOND_PER_MINUTE * 1.8);
        self::assertEquals(
            1.8,
            $time->convertToMinutes(1)
        );

        $time = new TimeInterval(self::SECOND_PER_MINUTE * 1.85);
        self::assertEquals(
            1.9,
            $time->convertToMinutes(1)
        );

        $time = new TimeInterval(self::SECOND_PER_MINUTE * 1.85);
        self::assertEquals(
            1.85,
            $time->convertToMinutes(2)
        );
    }

    /**
     * @covers TimeInterval::convertToSeconds
     */
    public function testConvertToSeconds()
    {
        // int value
        foreach ($this->getTestSeconds() as $seconds) {
            $time = new TimeInterval($seconds);
            self::assertEquals(
                $seconds,
                $time->convertToSeconds()
            );
        }

        // float value
        foreach ($this->getTestSecondsFloat() as $seconds) {
            $time = new TimeInterval($seconds);
            self::assertEquals(
                (int) $seconds,
                $time->convertToSeconds()
            );
        }
    }

    /**
     * @covers TimeInterval::getHours
     */
    public function testGetHours()
    {
        foreach ($this->getTestSeconds() as $seconds) {
            $time = new TimeInterval($seconds);
            self::assertEquals(
                (int) ($seconds / self::SECOND_PER_HOUR),
                $time->getHours()
            );
        }
    }

    /**
     * @covers TimeInterval::getMinutes
     */
    public function testGetMinutes()
    {
        foreach ($this->getTestSeconds() as $seconds) {
            $time = new TimeInterval($seconds);
            self::assertEquals(
                (int) ($seconds % self::SECOND_PER_HOUR / self::SECOND_PER_MINUTE),
                $time->getMinutes()
            );
        }
    }

    /**
     * @covers TimeInterval::getSeconds
     */
    public function testGetSeconds(): void
    {
        foreach ($this->getTestSeconds() as $seconds) {
            $seconds = $seconds + ($seconds / 2);

            $time = new TimeInterval($seconds);
            self::assertEquals(
                (int) ($seconds % self::SECOND_PER_HOUR % self::SECOND_PER_MINUTE),
                $time->getSeconds()
            );
        }
    }

    /**
     * @covers TimeInterval::createFromHMS
     */
    public function testCreateFromHMS()
    {
        foreach ($this->getTestSeconds() as $seconds) {
            $sign = $seconds < 0 ? -1 : 1;

            $timeHM = [
                abs((int) ($seconds / self::SECOND_PER_HOUR)),
                abs((int) ($seconds % self::SECOND_PER_HOUR / self::SECOND_PER_MINUTE)),
            ];

            $timeHMS = [
                abs((int) ($seconds / self::SECOND_PER_HOUR)),
                abs((int) ($seconds % self::SECOND_PER_HOUR / self::SECOND_PER_MINUTE)),
                abs((int) ($seconds % self::SECOND_PER_HOUR % self::SECOND_PER_MINUTE)),
            ];

            $time = TimeInterval::createFromHMS(($sign < 0 ? '-' : '') . implode(':', $timeHM));
            $secondsCalculate = (
                (abs($timeHM[0]) * self::SECOND_PER_HOUR) + $timeHM[1] * self::SECOND_PER_MINUTE
            ) * $sign;

            self::assertEquals(
                $secondsCalculate,
                $time->convertToSeconds()
            );

            self::assertEquals(
                (int) round($secondsCalculate / self::SECOND_PER_MINUTE),
                $time->convertToMinutes()
            );

            $time = TimeInterval::createFromHMS(($sign < 0 ? '-' : '') . implode(':', $timeHMS));
            self::assertEquals(
                $seconds,
                $time->convertToSeconds()
            );

            $time = TimeInterval::createFromHMS(($sign > 0 ? '+' : '-') . implode(':', $timeHMS));
            self::assertEquals($seconds, $time->convertToSeconds());
        }
    }

    /**
     * @covers TimeInterval::createFromHMS
     */
    public function testCreateFromHMSWrongString()
    {
        $this->expectException(InvalidArgumentException::class);
        TimeInterval::createFromHMS('*1:00:10');
    }

    /**
     * @covers TimeInterval::createFromHMS
     */
    public function testCreateFromHMSWhitespace()
    {
        $this->expectException(InvalidArgumentException::class);
        TimeInterval::createFromHMS(' 1:00:10');
    }

    /**
     * @covers TimeInterval::createFromDateString
     */
    public function testCreateFromDateString()
    {
        $dateString = '1 day + 12 hours';
        $time = TimeInterval::createFromDateString($dateString);
        self::assertEquals(
            86400 + 12 * 3600,
            $time->convertToSeconds()
        );

        $dateString = '1 year + 1 day + 12 hours';
        $this->expectException(InvalidArgumentException::class);
        TimeInterval::createFromDateString($dateString);
    }

    /**
     * @covers TimeInterval::createFromIntervalSpec
     * @throws Exception
     */
    public function testCreateFromIntervalSpec()
    {
        $intervalSpec = 'P1DT12H5M15S';
        $time = TimeInterval::createFromIntervalSpec($intervalSpec);
        self::assertEquals(
            86400 + 12 * 3600 + 15 * 60 + 15,
            $time->convertToSeconds()
        );

        $intervalSpec = 'PT3600S';
        $time = TimeInterval::createFromIntervalSpec($intervalSpec);
        self::assertEquals(
            3600,
            $time->convertToSeconds()
        );

        // Exception, only days, hours, minutes, seconds
        $intervalSpec = 'P1Y1D';
        $this->expectException(InvalidArgumentException::class);
        TimeInterval::createFromIntervalSpec($intervalSpec);
    }

    /**
     * @covers TimeInterval::createDateInterval
     *
     * @throws Exception
     */
    public function testCreateDateInterval()
    {
        $date = new DateTimeImmutable('2020-01-01 00:00:00');
        $time = new TimeInterval(4200);

        self::assertInstanceOf(DateInterval::class, $time->createDateInterval());

        self::assertEquals(
            (new DateTimeImmutable('2020-01-01 01:10:00')),
            $date->add($time->createDateInterval())
        );

        self::assertEquals(
            (new DateTimeImmutable('2019-12-31 22:50:00')),
            $date->sub($time->createDateInterval())
        );
    }

    /**
     * @covers TimeInterval::format()
     *
     * @throws Exception
     */
    public function testFormatSign()
    {
        foreach ($this->getTestSeconds() as $seconds) {
            $sign = ($seconds < 0) ? -1 : 1;
            $time = new TimeInterval($seconds);

            self::assertEquals(($sign < 0 ? '-' : '+'), $time->format('%R'));
            self::assertEquals(($sign < 0 ? '-' : ''), $time->format('%r'));
        }
    }

    /**
     * @covers TimeInterval::format()
     *
     * @throws Exception
     */
    public function testFormatHours()
    {
        $time = new TimeInterval(36447); // 10:04:07
        self::assertEquals('10', $time->format('%H'));
        self::assertEquals('10', $time->format('%h'));

        $time = new TimeInterval(7447); // 02:04:07
        self::assertEquals('02', $time->format('%H'));
        self::assertEquals('2', $time->format('%h'));

        $time = new TimeInterval(-7447); // 02:04:07
        self::assertEquals('02', $time->format('%H'));
        self::assertEquals('2', $time->format('%h'));
    }

    /**
     * @covers TimeInterval::format
     *
     * @throws Exception
     */
    public function testFormatMinutes()
    {
        $time = new TimeInterval(36247); // 10:04:07
        self::assertEquals('04', $time->format('%I'));
        self::assertEquals('4', $time->format('%i'));

        $time = new TimeInterval(650); // 00:10:50
        self::assertEquals('10', $time->format('%I'));
        self::assertEquals('10', $time->format('%i'));

        $time = new TimeInterval(-7447); // 02:04:07
        self::assertEquals('04', $time->format('%I'));
        self::assertEquals('4', $time->format('%i'));
    }

    /**
     * Тест форматирования секунд.
     *
     * @covers TimeInterval::format()
     *
     * @throws Exception
     */
    public function testFormatSeconds()
    {
        $time = new TimeInterval(36247); // 10:04:07
        self::assertEquals('07', $time->format('%S'));
        self::assertEquals('7', $time->format('%s'));

        $time = new TimeInterval(650); // 00:10:50
        self::assertEquals('50', $time->format('%S'));
        self::assertEquals('50', $time->format('%s'));

        $time = new TimeInterval(-7447); // 02:04:07
        self::assertEquals('07', $time->format('%S'));
        self::assertEquals('7', $time->format('%s'));
    }

    /**
     * Тест форматирования по шаблону.
     *
     * @covers TimeInterval::format()
     *
     * @throws Exception
     */
    public function testFormat()
    {
        $time = new TimeInterval(360000 + 86400 + 540 + 27);
        self::assertEquals('124:09:27', $time->format('%r%H:%I:%S'));
        self::assertEquals('+124:09:27', $time->format('%R%H:%I:%S'));

        $time = new TimeInterval(-1 * (360000 + 86400 + 540 + 27));
        self::assertEquals('-124:09:27', $time->format('%r%H:%I:%S'));
        self::assertEquals('-124:09:27', $time->format('%R%H:%I:%S'));

        $time = new TimeInterval(3600 * 2 + 240 + 7); // 02:04:07
        self::assertEquals('02:04:07', $time->format('%r%H:%I:%S'));
        self::assertEquals('+02:04:07', $time->format('%R%H:%I:%S'));
    }

    /**
     * Test data.
     *
     * @return int[]
     */
    protected function getTestSeconds(): array
    {
        return [
            0,
            5,
            1500,
            3600,
            10000,
            86715,
            -5,
            -1500,
            -3600,
            -10000,
            -86715
        ];
    }

    /**
     * Test data.
     *
     * @return float[]
     */
    protected function getTestSecondsFloat(): array
    {
        return [
            0.1,
            1.25,
            -0.3,
        ];
    }
}
