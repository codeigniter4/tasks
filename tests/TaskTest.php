<?php

use CodeIgniter\Tasks\Task;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

class TaskTest extends TestCase
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
}
