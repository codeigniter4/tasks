<?php

use CodeIgniter\Test\CIUnitTestCase as TestCase;

class FrequenciesTraitTest extends TestCase
{
    protected $class;

    public function setUp(): void
    {
        parent::setUp();

        $this->class = new class() {
            use \CodeIgniter\Tasks\FrequenciesTrait;
        };
    }

    public function testSetCron()
    {
        $cron = '5 10 11 12 6';

        $this->class->cron($cron);

        $this->assertEquals($cron, $this->class->getExpression());
    }

    public function testDaily()
    {
        $this->class->daily();

        $this->assertEquals('0 0 * * *', $this->class->getExpression());
    }

    public function testDailyWithTime()
    {
        $this->class->daily('4:08 pm');

        $this->assertEquals('08 16 * * *', $this->class->getExpression());
    }

    public function testHourly()
    {
        $this->class->hourly();

        $this->assertEquals('00 * * * *', $this->class->getExpression());
    }

    public function testEveryFiveMinutes()
    {
        $this->class->everyFiveMinutes();

        $this->assertEquals('/5 * * * *', $this->class->getExpression());
    }

    public function testEveryFifteenMinutes()
    {
        $this->class->everyFifteenMinutes();

        $this->assertEquals('/15 * * * *', $this->class->getExpression());
    }

    public function testEveryThirtyMinutes()
    {
        $this->class->everyThirtyMinutes();

        $this->assertEquals('/30 * * * *', $this->class->getExpression());
    }

    public function testEverySunday()
    {
        $this->class->everySunday();

        $this->assertEquals('* * * * 0', $this->class->getExpression());
    }

    public function testEverySundayWithTime()
    {
        $this->class->everySunday('4:08 pm');

        $this->assertEquals('08 16 * * 0', $this->class->getExpression());
    }

    public function testEveryMonday()
    {
        $this->class->everyMonday();

        $this->assertEquals('* * * * 1', $this->class->getExpression());
    }

    public function testEveryMondayWithTime()
    {
        $this->class->everyMonday('4:08 pm');

        $this->assertEquals('08 16 * * 1', $this->class->getExpression());
    }

    public function testEveryTuesday()
    {
        $this->class->everyTuesday();

        $this->assertEquals('* * * * 2', $this->class->getExpression());
    }

    public function testEveryTuesdayWithTime()
    {
        $this->class->everyTuesday('4:08 pm');

        $this->assertEquals('08 16 * * 2', $this->class->getExpression());
    }

    public function testEveryWednesday()
    {
        $this->class->everyWednesday();

        $this->assertEquals('* * * * 3', $this->class->getExpression());
    }

    public function testEveryWednesdayWithTime()
    {
        $this->class->everyWednesday('4:08 pm');

        $this->assertEquals('08 16 * * 3', $this->class->getExpression());
    }

    public function testEveryThursday()
    {
        $this->class->everyThursday();

        $this->assertEquals('* * * * 4', $this->class->getExpression());
    }

    public function testEveryThursdayWithTime()
    {
        $this->class->everyThursday('4:08 pm');

        $this->assertEquals('08 16 * * 4', $this->class->getExpression());
    }

    public function testEveryFriday()
    {
        $this->class->everyFriday();

        $this->assertEquals('* * * * 5', $this->class->getExpression());
    }

    public function testEveryFridayWithTime()
    {
        $this->class->everyFriday('4:08 pm');

        $this->assertEquals('08 16 * * 5', $this->class->getExpression());
    }

    public function testEverySaturday()
    {
        $this->class->everySaturday();

        $this->assertEquals('* * * * 6', $this->class->getExpression());
    }

    public function testEverySaturdayWithTime()
    {
        $this->class->everySaturday('4:08 pm');

        $this->assertEquals('08 16 * * 6', $this->class->getExpression());
    }

    public function testMonthly()
    {
        $this->class->monthly();

        $this->assertEquals('0 0 1 * *', $this->class->getExpression());
    }

    public function testMonthlyWithTime()
    {
        $this->class->monthly('4:08 pm');

        $this->assertEquals('08 16 1 * *', $this->class->getExpression());
    }

    public function testYearly()
    {
        $this->class->yearly();

        $this->assertEquals('0 0 1 1 *', $this->class->getExpression());
    }

    public function testYearlyWithTime()
    {
        $this->class->yearly('4:08 pm');

        $this->assertEquals('08 16 1 1 *', $this->class->getExpression());
    }

    public function testQuarterly()
    {
        $this->class->quarterly();

        $this->assertEquals('0 0 1 /3 *', $this->class->getExpression());
    }

    public function testQuarterlyWithTime()
    {
        $this->class->quarterly('4:08 pm');

        $this->assertEquals('08 16 1 /3 *', $this->class->getExpression());
    }

    public function testWeekdays()
    {
        $this->class->weekdays();

        $this->assertEquals('0 0 * * 1-5', $this->class->getExpression());
    }

    public function testWeekdaysWithTime()
    {
        $this->class->weekdays('4:08 pm');

        $this->assertEquals('08 16 * * 1-5', $this->class->getExpression());
    }

    public function testWeekends()
    {
        $this->class->weekends();

        $this->assertEquals('0 0 * * 6-7', $this->class->getExpression());
    }

    public function testWeekendsWithTime()
    {
        $this->class->weekends('4:08 pm');

        $this->assertEquals('08 16 * * 6-7', $this->class->getExpression());
    }
}
