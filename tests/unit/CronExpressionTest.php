<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Tasks.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use CodeIgniter\I18n\Time;
use CodeIgniter\Tasks\CronExpression;
use CodeIgniter\Tasks\Exceptions\TasksException;
use CodeIgniter\Test\CIUnitTestCase as TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class CronExpressionTest extends TestCase
{
    protected CronExpression $cron;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cron = new CronExpression();
    }

    public function testMinutes()
    {
        $this->assertTrue($this->cron->shouldRun('* * * * *'));

        $this->cron->testTime('2020-05-01 10:04 am');
        $this->assertFalse($this->cron->shouldRun('10 * * * *'));
        $this->assertTrue($this->cron->shouldRun('4 * * * *'));
        $this->assertTrue($this->cron->shouldRun('04 * * * *'));
        $this->assertTrue($this->cron->shouldRun('4,8 * * * *'));
        $this->assertTrue($this->cron->shouldRun('1,2,4 * * * *'));
        $this->assertFalse($this->cron->shouldRun('5-15 * * * *'));
        $this->assertTrue($this->cron->shouldRun('1-5 * * * *'));
        $this->assertTrue($this->cron->shouldRun('/4 * * * *'));
        $this->assertTrue($this->cron->shouldRun('/2 * * * *'));
        $this->assertFalse($this->cron->shouldRun('/5 * * * *'));
    }

    public function testMinutesInvalidNoNumber()
    {
        $this->cron->testTime('2020-05-01 10:04 am');

        $this->expectException(TasksException::class);
        $this->expectExceptionMessage('"/ * * * *" is not a valid cron expression.');

        $this->assertFalse($this->cron->shouldRun('/ * * * *'));
    }

    public function testMinutesInvalidNotANumber()
    {
        $this->cron->testTime('2020-05-01 10:04 am');

        $this->expectException(TasksException::class);
        $this->expectExceptionMessage('"/a * * * *" is not a valid cron expression.');

        $this->assertFalse($this->cron->shouldRun('/a * * * *'));
    }

    public function testHours()
    {
        $this->cron->testTime('2020-05-01 10:04 am');

        $this->assertTrue($this->cron->shouldRun('* * * * *'));
        $this->assertTrue($this->cron->shouldRun('* 10 * * *'));
        $this->assertFalse($this->cron->shouldRun('* 20 * * *'));
        $this->assertTrue($this->cron->shouldRun('4 10 * * *'));
        $this->assertFalse($this->cron->shouldRun('10 10 * * *'));
        $this->assertTrue($this->cron->shouldRun('* 10,11 * * *'));
        $this->assertTrue($this->cron->shouldRun('* 9,11,10 * * *'));
        $this->assertFalse($this->cron->shouldRun('* 9,11,12 * * *'));
        $this->assertTrue($this->cron->shouldRun('* 8-11 * * *'));
        $this->assertFalse($this->cron->shouldRun('* 7-9 * * *'));
        $this->assertTrue($this->cron->shouldRun('* /2 * * *'));
        $this->assertTrue($this->cron->shouldRun('* /5 * * *'));
        $this->assertFalse($this->cron->shouldRun('* /3 * * *'));
    }

    public function testMonthDay()
    {
        $this->cron->testTime('2020-05-01 10:04 am');

        $this->assertTrue($this->cron->shouldRun('* * 1 * *'));
        $this->assertTrue($this->cron->shouldRun('* * 01 * *'));
        $this->assertFalse($this->cron->shouldRun('* * 02 * *'));
        $this->assertTrue($this->cron->shouldRun('04 10 1 * *'));
        $this->assertFalse($this->cron->shouldRun('05 10 1 * *'));
        $this->assertFalse($this->cron->shouldRun('04 11 1 * *'));
        $this->assertTrue($this->cron->shouldRun('* * 1,2 * *'));
        $this->assertFalse($this->cron->shouldRun('* * 3,2 * *'));
        $this->assertTrue($this->cron->shouldRun('* * 1-3 * *'));
        $this->assertFalse($this->cron->shouldRun('* * 3-5 * *'));
        $this->assertTrue($this->cron->shouldRun('* * /1 * *'));
        $this->assertFalse($this->cron->shouldRun('* * /2 * *'));
    }

    public function testMonth()
    {
        $this->cron->testTime('2020-05-01 10:04 am');

        $this->assertTrue($this->cron->shouldRun('* * * 5 *'));
        $this->assertFalse($this->cron->shouldRun('* * * 6 *'));
        $this->assertTrue($this->cron->shouldRun('* * * 5,6 *'));
        $this->assertFalse($this->cron->shouldRun('* * * 4,6 *'));
        $this->assertTrue($this->cron->shouldRun('* * * 4-6 *'));
        $this->assertFalse($this->cron->shouldRun('* * * 6-8 *'));
        $this->assertTrue($this->cron->shouldRun('* * * /5 *'));
        $this->assertFalse($this->cron->shouldRun('* * * /2 *'));
    }

    public function testWeekDay()
    {
        // May 1 is s Friday
        $this->cron->testTime('2020-05-01 10:04 am');

        $this->assertTrue($this->cron->shouldRun('* * * * 5'));
        $this->assertFalse($this->cron->shouldRun('* * * * 6'));
        $this->assertTrue($this->cron->shouldRun('* * * * 5,6'));
        $this->assertFalse($this->cron->shouldRun('* * * * 4,6'));
        $this->assertFalse($this->cron->shouldRun('* * * * 1-3'));
        $this->assertTrue($this->cron->shouldRun('* * * * 4-6'));
        $this->assertTrue($this->cron->shouldRun('* * * * /5'));
        $this->assertFalse($this->cron->shouldRun('* * * * /2'));
    }

    public function testHoursAndMins()
    {
        $this->cron->testTime('6:30 PM');
        $this->assertTrue($this->cron->shouldRun('30 18 * * *'));
    }

    /**
     * @param mixed $hourTrue
     * @param mixed $hourFalse
     */
    #[DataProvider('provideEveryHour')]
    public function testEveryHour($hourTrue, $hourFalse)
    {
        $this->cron->testTime($hourTrue);
        $this->assertTrue($this->cron->shouldRun('00 * * * *'));

        $this->cron->testTime($hourFalse);
        $this->assertFalse($this->cron->shouldRun('00 * * * *'));
    }

    public function testQuarterPastHour()
    {
        $this->cron->testTime('6:15 PM');
        $this->assertTrue($this->cron->shouldRun('15 * * * *'));

        $this->cron->testTime('6:30 PM');
        $this->assertFalse($this->cron->shouldRun('15 * * * *'));
    }

    public function testSpecificDate()
    {
        $this->cron->testTime('January 1, 2020 12:30 am');
        $this->assertTrue($this->cron->shouldRun('30 0 1 1,6,12 *'));

        $this->cron->testTime('June 1, 2020 12:30 am');
        $this->assertTrue($this->cron->shouldRun('30 0 1 1,6,12 *'));

        $this->cron->testTime('December 1, 2020 12:30 am');
        $this->assertTrue($this->cron->shouldRun('30 0 1 1,6,12 *'));

        $this->cron->testTime('February 1, 2020 12:30 am');
        $this->assertFalse($this->cron->shouldRun('30 0 1 1,6,12 *'));
    }

    public function testEveryWeekdayInMonth()
    {
        // October 5th is a Monday
        $this->cron->testTime('October 5, 2020 8:00 pm');
        $this->assertTrue($this->cron->shouldRun('0 20 * 10 1-5'));

        $this->cron->testTime('October 4, 2020 8:00 pm');
        $this->assertFalse($this->cron->shouldRun('0 20 * 10 1-5'));

        // Another Monday
        $this->cron->testTime('November 2, 2020 8:00 pm');
        $this->assertFalse($this->cron->shouldRun('0 20 * 10 1-5'));
    }

    public static function provideEveryHour(): iterable
    {
        $hours24 = array_map(static fn ($h) => [
            $h . ':00',
            $h . ':10',
        ], range(0, 23));
        $hoursAM = array_map(static fn ($h) => [
            $h . ':00 AM',
            $h . ':10 AM',
        ], range(1, 12));
        $hoursPM = array_map(static fn ($h) => [
            $h . ':00 PM',
            $h . ':10 PM',
        ], range(1, 12));

        return [...$hours24, ...$hoursAM, ...$hoursPM];
    }

    public static function provideNextRun(): iterable
    {
        return [
            ['* * * * *', 'October 5, 2020 8:01 pm'],
            ['4 * * * *', 'October 5, 2020 8:04 pm'],
            ['5-10 * * * *', 'October 5, 2020 8:05 pm'],
            ['57,3,8,10 * * * *', 'October 5, 2020 8:03 pm'],
            ['*/5 * * * *', 'October 5, 2020 8:05 pm'],
            ['30/5 * * * *', 'October 5, 2020 8:30 pm'],
            ['* 6 * * *', 'October 6, 2020 6:00 am'],
            ['* 12-14 * * *', 'October 6, 2020 12:00 pm'],
            ['* 5,6 * * *', 'October 6, 2020 5:00 am'],
            ['* 2/4 * * *', 'October 5, 2020 10:00 pm'],
            ['5 10 * * *', 'October 6, 2020 10:05 am'],
            ['* * 10 * *', 'October 10, 2020 8:00 pm'],
            ['5 4 10 * *', 'October 10, 2020 4:05 am'],
            ['* * * 3 *', 'March 5, 2021 8:00 pm'],
            ['* 4/5 12 3 *', 'March 12, 2021 4:00 am'],
            ['* * * * Wed', 'October 6, 2020 8:00 pm'],
            ['* * * * 3', 'October 6, 2020 8:00 pm'],
            ['* * * * 6,0', 'October 9, 2020 8:00 pm'],
        ];
    }

    #[DataProvider('provideNextRun')]
    public function testNextRun(string $exp, string $expected)
    {
        $this->cron->testTime('October 5, 2020 8:00 pm');

        $next = $this->cron->nextRun($exp);

        $this->assertInstanceOf(Time::class, $next);
        $this->assertSame($expected, $next->format('F j, Y g:i a'));
    }
}
