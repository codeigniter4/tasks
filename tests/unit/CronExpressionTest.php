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
