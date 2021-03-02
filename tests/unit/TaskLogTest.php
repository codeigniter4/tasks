<?php

use CodeIgniter\Test\CIUnitTestCase as TestCase;
use CodeIgniter\I18n\Time;
use CodeIgniter\Tasks\TaskLog;

class TaskLogTest extends TestCase
{
	public function durationProvider()
	{
		return [
			[
				'2021-01-21 12:00:00',
				'2021-01-21 12:00:00',
				'00:00',
			],
			[
				'2021-01-21 12:00:00',
				'2021-01-21 12:00:01',
				'00:01',
			],
			[
				'2021-01-21 12:00:00',
				'2021-01-21 12:05:12',
				'05:12',
			],
		];
	}

	/**
	 * @dataProvider durationProvider
	 */
	public function testDuration($start, $end, $expected)
	{
		$start = new Time($start);
		$end   = new Time($end);

		$log = new TaskLog([
			'task'     => function () {
			},
			'output'   => '',
			'runStart' => $start,
			'runEnd'   => $end,
			'error'    => null,
		]);

		$this->assertEquals($expected, $log->duration());
	}
}
