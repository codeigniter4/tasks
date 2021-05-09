<?php


use CodeIgniter\I18n\Time;
use CodeIgniter\Tasks\TaskLog;
use Tests\Support\DatabaseTestCase;

class TaskPerformanceLogTest extends DatabaseTestCase
{

	public function testInsertPerformanceLog()
	{
		$task = new \CodeIgniter\Tasks\Task("closure", function() {
			return "Test";
		});

		$task->named("foo");

		$task->enablePerformance();

		$taskLog = new TaskLog([
			'task' => $task,
			'output' => "Test",
			'runStart' => Time::now(),
			'runEnd' => Time::now(),
			'error' => "",
		]);

		// Save performance log to the database
		$taskLog->saveToDatabase();

		$countRecords = db_connect()->table("tasks_performance")->countAll();

		// Validate Log Record is saved
		$this->assertEquals(1, $countRecords);
	}

	public function testIfPerformanceIsNotEnabled()
	{

		$task = new \CodeIgniter\Tasks\Task("closure", function() {
			return "Test";
		});

		$task->named("foo");

		$taskLog = new TaskLog([
			'task' => $task,
			'output' => "Test",
			'runStart' => Time::now(),
			'runEnd' => Time::now(),
			'error' => "",
		]);

		// Save performance log to the database
		$taskLog->saveToDatabase();

		$countRecords = db_connect()->table("tasks_performance")->countAll();

		// Validate If Log Record Is Saved
		$this->assertEquals(0, $countRecords);
	}

	public function testTaskOutput()
	{
		$task = new \CodeIgniter\Tasks\Task("closure", function() {
			return "Test";
		});

		$task->named("foo");

		$output = $task->run();

		$this->assertEquals("Test", $output);
	}
}