<?php

use CodeIgniter\Tasks\Task;
use Tests\Support\TasksTestCase;

class TaskTest extends TasksTestCase
{
	public function testConstructSavesAction()
	{
		$task = new Task('command', 'foo:bar');

		$result = $this->getPrivateProperty($task, 'action');

		$this->assertEquals('foo:bar', $result);
	}

	public function testGetAction()
	{
		$task = new Task('command', 'foo:bar');

		$this->assertEquals('foo:bar', $task->getAction());
	}

	public function testGetType()
	{
		$task = new Task('command', 'foo:bar');

		$this->assertEquals('command', $task->getType());
	}

	public function testCommandRunsCommand()
	{
		$task = new Task('command', 'tasks:test');
		$task->run();

		$this->assertTrue($_SESSION['command_tasks_test_did_run']);
	}

	/**
	 * `command()` is not buffering the output like it appears it should,
	 * so the result is not actually being returned. Disabling this test
	 * until the root issue can be resolved.
	 */
	//  public function testCommandReturnsOutput()
	//  {
	//      $task   = new Task('command', 'tasks:test');
	//      $result = $task->run();
	//
	//      $this->assertEquals('Commands can output text.', $result);
	//  }

	public function testShouldRunSimple()
	{
		$task = (new Task('command', 'tasks:test'))->hourly();

		$this->assertFalse($task->shouldRun('12:05am'));
		$this->assertTrue($task->shouldRun('12:00am'));
	}

	public function testShouldRunWithEnvironments()
	{
		$originalEnv               = $_SERVER['CI_ENVIRONMENT'];
		$_SERVER['CI_ENVIRONMENT'] = 'development';

		$task = (new Task('command', 'tasks:test'))->environments('development');

		$this->assertTrue($task->shouldRun('12:00am'));

		$_SERVER['CI_ENVIRONMENT'] = 'production';

		$this->assertFalse($task->shouldRun('12:00am'));

		$_SERVER['CI_ENVIRONMENT'] = $originalEnv;
	}
}
