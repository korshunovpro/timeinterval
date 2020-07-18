<?php

namespace Korshunov\TimeInterval\Tests;

use Korshunov\TimeInterval\TimeInterval;
use Korshunov\TimeInterval\TimeIntervalInterface;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * Class TimeIntervalTest.
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

        $this->assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
        $this->assertEquals(self::SECOND_PER_HOUR, $time2->convertToSeconds());
        $this->assertEquals(self::SECOND_PER_HOUR, $time3->convertToSeconds());
        $this->assertEquals(-1 * self::SECOND_PER_HOUR, $time4->convertToSeconds());

        $this->expectException(InvalidArgumentException::class);
        new TimeInterval(self::SECOND_PER_HOUR, self::FAKE_WRONG_TIME_UNIT);
    }

    /**
     * @covers TimeInterval::modify
     */
    public function testModify()
    {
        $time = new TimeInterval(self::SECOND_PER_HOUR);

        $time->modify(55);
        $this->assertEquals(55 + self::SECOND_PER_HOUR, $time->convertToSeconds());

        $time->modify(-55);
        $this->assertEquals(self::SECOND_PER_HOUR, $time->convertToSeconds());

        $time->modify(1, TimeIntervalInterface::HOUR);
        $this->assertEquals(2 * self::SECOND_PER_HOUR, $time->convertToSeconds());

        $time->modify(-1, TimeIntervalInterface::HOUR);
        $this->assertEquals(self::SECOND_PER_HOUR, $time->convertToSeconds());
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
        $this->assertEquals(2 * self::SECOND_PER_HOUR, $time1->convertToSeconds());

        $time1->add($time3Negative);
        $this->assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
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
        $this->assertEquals(0, $time1->convertToSeconds());

        $time1->sub($time3Negative);
        $this->assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
    }

    /**
     * @covers TimeInterval::convertToHours
     */
    public function testConvertToHours()
    {
        $time = new TimeInterval(self::SECOND_PER_HOUR);
        $this->assertEquals(
            1,
            $time->convertToHours()
        );

        $time = new TimeInterval(self::SECOND_PER_HOUR * 1.8);
        $this->assertEquals(
            1.8,
            $time->convertToHours(1)
        );

        $time = new TimeInterval(self::SECOND_PER_HOUR * 1.85);
        $this->assertEquals(
            1.9,
            $time->convertToHours(1)
        );

        $time = new TimeInterval(self::SECOND_PER_HOUR * 1.85);
        $this->assertEquals(
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
        $this->assertEquals(
            1,
            $time->convertToMinutes()
        );

        $time = new TimeInterval(self::SECOND_PER_MINUTE * 1.8);
        $this->assertEquals(
            1.8,
            $time->convertToMinutes(1)
        );

        $time = new TimeInterval(self::SECOND_PER_MINUTE * 1.85);
        $this->assertEquals(
            1.9,
            $time->convertToMinutes(1)
        );

        $time = new TimeInterval(self::SECOND_PER_MINUTE * 1.85);
        $this->assertEquals(
            1.85,
            $time->convertToMinutes(2)
        );
    }

    /**
     * @covers TimeInterval::convertToSeconds
     */
    public function testConvertToSeconds()
    {
        $time = new TimeInterval(self::SECOND_PER_MINUTE);
        $this->assertEquals(
            self::SECOND_PER_MINUTE,
            $time->convertToSeconds()
        );
    }

    /**
     * @covers TimeInterval::getHours
     */
    public function testGetHours()
    {

    }

    /**
     * @covers TimeInterval::getMinutes
     */
    public function testGetSeconds()
    {

    }

    /**
     * @covers TimeInterval::getSeconds
     */
    public function testGetMinutes()
    {

    }

    public function testCreateFromHMS()
    {

    }



    public function testCreateFromDateString()
    {

    }

    public function testFormat()
    {

    }

    public function testCreateDateInterval()
    {

    }
}
