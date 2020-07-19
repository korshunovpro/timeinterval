<?php

namespace Korshunov\TimeInterval\Tests;

use Korshunov\TimeInterval\TimeIntervalImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Class TimeIntervalImmutableTest.
 *
 * @coversDefaultClass TimeIntervalImmutable
 */
class TimeIntervalImmutableTest extends TestCase
{
    // Constants
    protected const SECOND_PER_DAY = 86400;
    protected const SECOND_PER_HOUR = 3600;
    protected const SECOND_PER_MINUTE = 60;

    /**
     * @covers TimeIntervalImmutable::modify
     */
    public function testModify()
    {
        $time = new TimeIntervalImmutable(self::SECOND_PER_HOUR);

        $time2 = $time->modify(55);
        $this->assertEquals(self::SECOND_PER_HOUR, $time->convertToSeconds());
        $this->assertEquals(55 + self::SECOND_PER_HOUR, $time2->convertToSeconds());
    }

    /**
     * @covers TimeIntervalImmutable::add
     */
    public function testAdd()
    {
        $time1 = new TimeIntervalImmutable(self::SECOND_PER_HOUR);
        $time2 = new TimeIntervalImmutable(self::SECOND_PER_HOUR);

        $time3 = $time1->add($time2);
        $this->assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
        $this->assertEquals(2 * self::SECOND_PER_HOUR, $time3->convertToSeconds());
    }

    /**
     * @covers TimeIntervalImmutable::sub
     */
    public function testSub()
    {
        $time1 = new TimeIntervalImmutable(self::SECOND_PER_HOUR);
        $time2 = new TimeIntervalImmutable(self::SECOND_PER_HOUR);

        $time3 = $time1->sub($time2);
        $this->assertEquals(self::SECOND_PER_HOUR, $time1->convertToSeconds());
        $this->assertEquals(0, $time3->convertToSeconds());
    }
}
