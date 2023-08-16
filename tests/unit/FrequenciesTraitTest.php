<?php

declare(strict_types=1);

use CodeIgniter\Tasks\FrequenciesTrait;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

/**
 * @internal
 */
final class FrequenciesTraitTest extends TestCase
{
    protected object $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->class = new class () {
            use FrequenciesTrait;
        };
    }

    public function testSetCron()
    {
        $cron = '5 10 11 12 6';

        $this->class->cron($cron);

        $this->assertSame($cron, $this->class->getExpression());
    }

    public function testDaily()
    {
        $this->class->daily();

        $this->assertSame('0 0 * * *', $this->class->getExpression());
    }

    public function testDailyWithTime()
    {
        $this->class->daily('4:08 pm');

        $this->assertSame('08 16 * * *', $this->class->getExpression());
    }

    public function testTime()
    {
        $this->class->time('4:08 pm');

        $this->assertSame('08 16 * * *', $this->class->getExpression());
    }

    public function testHourly()
    {
        $this->class->hourly();

        $this->assertSame('00 * * * *', $this->class->getExpression());
    }

    public function testHourlyWithMinutes()
    {
        $this->class->hourly(30);

        $this->assertSame('30 * * * *', $this->class->getExpression());
    }

    public function testEveryFiveMinutes()
    {
        $this->class->everyFiveMinutes();

        $this->assertSame('*/5 * * * *', $this->class->getExpression());
    }

    public function testEveryFifteenMinutes()
    {
        $this->class->everyFifteenMinutes();

        $this->assertSame('*/15 * * * *', $this->class->getExpression());
    }

    public function testEveryThirtyMinutes()
    {
        $this->class->everyThirtyMinutes();

        $this->assertSame('*/30 * * * *', $this->class->getExpression());
    }

    public function testEverySunday()
    {
        $this->class->sundays();

        $this->assertSame('* * * * 0', $this->class->getExpression());
    }

    public function testEverySundayWithTime()
    {
        $this->class->sundays('4:08 pm');

        $this->assertSame('08 16 * * 0', $this->class->getExpression());
    }

    public function testEveryMonday()
    {
        $this->class->mondays();

        $this->assertSame('* * * * 1', $this->class->getExpression());
    }

    public function testEveryMondayWithTime()
    {
        $this->class->mondays('4:08 pm');

        $this->assertSame('08 16 * * 1', $this->class->getExpression());
    }

    public function testEveryTuesday()
    {
        $this->class->tuesdays();

        $this->assertSame('* * * * 2', $this->class->getExpression());
    }

    public function testEveryTuesdayWithTime()
    {
        $this->class->tuesdays('4:08 pm');

        $this->assertSame('08 16 * * 2', $this->class->getExpression());
    }

    public function testEveryWednesday()
    {
        $this->class->wednesdays();

        $this->assertSame('* * * * 3', $this->class->getExpression());
    }

    public function testEveryWednesdayWithTime()
    {
        $this->class->wednesdays('4:08 pm');

        $this->assertSame('08 16 * * 3', $this->class->getExpression());
    }

    public function testEveryThursday()
    {
        $this->class->thursdays();

        $this->assertSame('* * * * 4', $this->class->getExpression());
    }

    public function testEveryThursdayWithTime()
    {
        $this->class->thursdays('4:08 pm');

        $this->assertSame('08 16 * * 4', $this->class->getExpression());
    }

    public function testEveryFriday()
    {
        $this->class->fridays();

        $this->assertSame('* * * * 5', $this->class->getExpression());
    }

    public function testEveryFridayWithTime()
    {
        $this->class->fridays('4:08 pm');

        $this->assertSame('08 16 * * 5', $this->class->getExpression());
    }

    public function testEverySaturday()
    {
        $this->class->saturdays();

        $this->assertSame('* * * * 6', $this->class->getExpression());
    }

    public function testEverySaturdayWithTime()
    {
        $this->class->saturdays('4:08 pm');

        $this->assertSame('08 16 * * 6', $this->class->getExpression());
    }

    public function testMonthly()
    {
        $this->class->monthly();

        $this->assertSame('0 0 1 * *', $this->class->getExpression());
    }

    public function testMonthlyWithTime()
    {
        $this->class->monthly('4:08 pm');

        $this->assertSame('08 16 1 * *', $this->class->getExpression());
    }

    public function testYearly()
    {
        $this->class->yearly();

        $this->assertSame('0 0 1 1 *', $this->class->getExpression());
    }

    public function testYearlyWithTime()
    {
        $this->class->yearly('4:08 pm');

        $this->assertSame('08 16 1 1 *', $this->class->getExpression());
    }

    public function testQuarterly()
    {
        $this->class->quarterly();

        $this->assertSame('0 0 1 */3 *', $this->class->getExpression());
    }

    public function testQuarterlyWithTime()
    {
        $this->class->quarterly('4:08 pm');

        $this->assertSame('08 16 1 */3 *', $this->class->getExpression());
    }

    public function testWeekdays()
    {
        $this->class->weekdays();

        $this->assertSame('0 0 * * 1-5', $this->class->getExpression());
    }

    public function testWeekdaysWithTime()
    {
        $this->class->weekdays('4:08 pm');

        $this->assertSame('08 16 * * 1-5', $this->class->getExpression());
    }

    public function testWeekends()
    {
        $this->class->weekends();

        $this->assertSame('0 0 * * 6-7', $this->class->getExpression());
    }

    public function testWeekendsWithTime()
    {
        $this->class->weekends('4:08 pm');

        $this->assertSame('08 16 * * 6-7', $this->class->getExpression());
    }

    public function testEveryHour()
    {
        $this->class->everyHour();

        $this->assertSame('0 * * * *', $this->class->getExpression());
    }

    public function testEveryHourWithHour()
    {
        $this->class->everyHour(3);

        $this->assertSame('0 */3 * * *', $this->class->getExpression());
    }

    public function testEveryHourWithHourAndMinutes()
    {
        $this->class->everyHour(3, 15);

        $this->assertSame('15 */3 * * *', $this->class->getExpression());
    }

    public function testBetweenHours()
    {
        $this->class->betweenHours(10, 12);

        $this->assertSame('* 10-12 * * *', $this->class->getExpression());
    }

    public function testHours()
    {
        $this->class->hours([12, 16]);

        $this->assertSame('* 12,16 * * *', $this->class->getExpression());
    }

    public function testEveryMinute()
    {
        $this->class->everyMinute();

        $this->assertSame('* * * * *', $this->class->getExpression());
    }

    public function testEveryMinuteWithParameter()
    {
        $this->class->everyMinute(15);

        $this->assertSame('*/15 * * * *', $this->class->getExpression());
    }

    public function testBetweenMinutes()
    {
        $this->class->betweenMinutes(15, 30);

        $this->assertSame('15-30 * * * *', $this->class->getExpression());
    }

    public function testMinutes()
    {
        $this->class->minutes([0, 10, 30]);

        $this->assertSame('0,10,30 * * * *', $this->class->getExpression());
    }

    public function testDays()
    {
        $this->class->days([0, 4]);

        $this->assertSame('* * * * 0,4', $this->class->getExpression());
    }

    public function testDaysOfMonth()
    {
        $this->class->daysOfMonth([1, 15]);

        $this->assertSame('* * 1,15 * *', $this->class->getExpression());
    }

    public function testMonths()
    {
        $this->class->months([1, 7]);

        $this->assertSame('* * * 1,7 *', $this->class->getExpression());
    }
}
