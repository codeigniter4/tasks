<?php

use CodeIgniter\Tasks\CronExpression;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

class CronExpressionTest extends TestCase
{
	/**
	 * @var CronExpression
	 */
	protected $cron;

	public function setUp(): void
	{
		parent::setUp();

		$this->cron = new CronExpression();
	}

	/**
	 * @dataProvider timeAndTimezoneProvider
	 */
	public function testMinutes($date, $tz)
	{
		$this->assertTrue($this->cron->shouldRun('* * * * *', $tz));

		$this->cron->testTime($date);
		$this->assertFalse($this->cron->shouldRun('10 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('4 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('04 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('4,8 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('1,2,4 * * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('5-15 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('1-5 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('/4 * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('/2 * * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('/5 * * * *', $tz));
	}

	/**
	 * @dataProvider timeAndTimezoneProvider
	 */
	public function testHours($date, $tz)
	{
		$this->cron->testTime($date);

		$this->assertTrue($this->cron->shouldRun('* * * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* 10 * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* 20 * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('4 10 * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('10 10 * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* 10,11 * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* 9,11,10 * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* 9,11,12 * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* 8-11 * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* 7-9 * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* /2 * * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* /5 * * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* /3 * * *', $tz));
	}

	/**
	 * @dataProvider timeAndTimezoneProvider
	 */
	public function testMonthDay($date, $tz)
	{
		$this->cron->testTime($date);

		$this->assertTrue($this->cron->shouldRun('* * 1 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * 01 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * 02 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('04 10 1 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('05 10 1 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('04 11 1 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * 1,2 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * 3,2 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * 1-3 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * 3-5 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * /1 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * /2 * *', $tz));
	}

	/**
	 * @dataProvider timeAndTimezoneProvider
	 */
	public function testMonth($date, $tz)
	{
		$this->cron->testTime($date);

		$this->assertTrue($this->cron->shouldRun('* * * 5 *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * 6 *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * * 5,6 *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * 4,6 *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * * 4-6 *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * 6-8 *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * * /5 *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * /2 *', $tz));
	}

	/**
	 * @dataProvider timeAndTimezoneProvider
	 */
	public function testWeekDay($date, $tz)
	{
		// May 1 is s Friday
		$this->cron->testTime($date);

		$this->assertTrue($this->cron->shouldRun('* * * * 5', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * * 6', $tz));
		$this->assertTrue($this->cron->shouldRun('* * * * 5,6', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * * 4,6', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * * 1-3', $tz));
		$this->assertTrue($this->cron->shouldRun('* * * * 4-6', $tz));
		$this->assertTrue($this->cron->shouldRun('* * * * /5', $tz));
		$this->assertFalse($this->cron->shouldRun('* * * * /2', $tz));
	}

	public function testHoursAndMins()
	{
		$this->cron->testTime('6:30 PM');
		$this->assertTrue($this->cron->shouldRun('30 18 * * *'));
	}

	/**
	 * @dataProvider hoursProvider
	 */
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

	public function testValidTimezone()
	{
		$this->cron->timezone('America/Chicago');
		$check = $this->getPrivateProperty($this->cron, 'timezone');
		$this->assertEquals(new \DateTimeZone('America/Chicago'), $check);

		$this->expectException(Exception::class);

		// This should throw an InvalidArgumentException
		$this->cron->timezone('NotAReal/Timezone');
	}

	public function testMinutesWithTimezone()
	{
		// Test minutes with Half Hour timezone
		$this->cron->testTime('10:34 am GMT');
		$this->assertFalse($this->cron->shouldRun('10 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('4 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('04 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('4,8 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('1,2,4 * * * *', 'GMT+5:30'));
		$this->assertFalse($this->cron->shouldRun('5-15 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('1-5 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('/4 * * * *', 'GMT+5:30'));
		$this->assertTrue($this->cron->shouldRun('/2 * * * *', 'GMT+5:30'));
		$this->assertFalse($this->cron->shouldRun('/5 * * * *', 'GMT+5:30'));
	}

	public function testHoursWithTimezone()
	{
		// Setting testTime to the wrong time,
		// we will fix with timezone adding 1 h
		$this->cron->testTime('9:04 am GMT');

		$this->assertTrue($this->cron->shouldRun('* * * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 10 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* 20 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('4 10 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('10 10 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 10,11 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 9,11,10 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* 9,11,12 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 8-11 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* 7-9 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* /2 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* /5 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* /3 * * *', 'GMT+1'));
	}

	/**
	 * @dataProvider timeAndTimezoneProvider
	 */
	public function testWithProvider($date, $tz)
	{
		$this->cron->testTime($date);

		$this->assertTrue($this->cron->shouldRun('* * 1 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * 01 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * 02 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('04 10 1 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('05 10 1 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('04 11 1 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * 1,2 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * 3,2 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * 1-3 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * 3-5 * *', $tz));
		$this->assertTrue($this->cron->shouldRun('* * /1 * *', $tz));
		$this->assertFalse($this->cron->shouldRun('* * /2 * *', $tz));
	}

	public function testHoursWithDifferentTz()
	{
		// Setting testTime to the wrong time,
		// we will fix with timezone adding 1 h
		$this->cron->timezone('GMT');
		$this->cron->testTime('9:04 am GMT+1');

		$this->assertTrue($this->cron->shouldRun('* * * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 11 * * *', 'GMT+2'));
		$this->assertFalse($this->cron->shouldRun('* 9 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('4 11 * * *', 'GMT+2'));
		$this->assertFalse($this->cron->shouldRun('10 10 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 10,11 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 9,11,10 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* 9,11,12 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* 8-11 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* 7-9 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* /2 * * *', 'GMT+1'));
		$this->assertTrue($this->cron->shouldRun('* /5 * * *', 'GMT+1'));
		$this->assertFalse($this->cron->shouldRun('* /3 * * *', 'GMT+1'));

		// DST Check, If Athens is in DST, Sydney is not and vice versa
		$athTz     = new DateTime('10:00 Europe/Athens');
		$athAssert = $athTz->format('I') === 1 ? true : false;
		$this->assertEquals($this->cron->shouldRun('* 12 * * *', 'Europe/Athens'), $athAssert);
		$this->assertEquals($this->cron->shouldRun('* 11 * * *', 'Europe/Athens'), ! $athAssert);

		$auTz     = new DateTime('10:00 Australia/Sydney');
		$auAssert = $auTz->format('I') === 1 ? true : false;
		$this->assertEquals($this->cron->shouldRun('* 20 * * *', 'Australia/Sydney'), $auAssert);
		$this->assertEquals($this->cron->shouldRun('* 19 * * *', 'Australia/Sydney'), ! $auAssert);
	}

	public function testSetTimezoneWithConstructor()
	{
		$cron  = new CronExpression('UTC');
		$check = $this->getPrivateProperty($cron, 'timezone');
		$this->assertEquals(new \DateTimeZone('UTC'), $check);

		$this->expectException(Exception::class);
		$cron  = new CronExpression('Not A Real\Timezone');
		$check = $this->getPrivateProperty($cron, 'timezone');
		$this->assertEquals(new \DateTimeZone('UTC'), $check);
	}
	public function testRuntimeTimeZoneWithGlobalTimezoneSet()
	{
		$cron  = new CronExpression('UTC');
		$check = $this->getPrivateProperty($cron, 'timezone');
		$cron->testTime('10:00 AM');
		//Argentina does not observe DST, makes this test work no matter the year
		$this->assertTrue($cron->shouldRun('* 7 * * * *', 'America/Argentina/Buenos_Aires'));
		$this->assertFalse($cron->shouldRun('* 10 * * * *', 'America/Argentina/Buenos_Aires'));
	}

	public function timeAndTimezoneProvider()
	{
		return [
			[
				'2020-05-01 10:04 am',
				null,
			],
			[
				'2020-05-01 09:04 am GMT',
				'GMT+1',
			],
		];
	}

	public function hoursProvider()
	{
		$hours24 = array_map(function ($h) {
			return [
				$h . ':00',
				$h . ':10',
			];
		}, range(0, 23));
		$hoursAM = array_map(function ($h) {
			return [
				$h . ':00 AM',
				$h . ':10 AM',
			];
		}, range(1, 12));
		$hoursPM = array_map(function ($h) {
			return [
				$h . ':00 PM',
				$h . ':10 PM',
			];
		}, range(1, 12));
		return array_merge($hours24, $hoursAM, $hoursPM);
	}
}
