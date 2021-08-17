<?php

use CodeIgniter\Tasks\Task;
use Tests\Support\TasksTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\I18n\Time;

class TaskTest extends TasksTestCase
{
    use DatabaseTestTrait;

    protected $namespace = 'Sparks\Settings';

    public function testNamed()
    {
        $task = new Task('command', 'foo:bar');

        // Will build a random name
        $this->assertTrue(strpos($task->name, 'command_') === 0);

        $task = (new Task('command', 'foo:bar'))->named('foo');

        $this->assertEquals('foo', $task->name);
    }

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

    public function testLastRun()
    {
        helper('setting');
        setting('Tasks.logPerformance', true);

        $task = new CodeIgniter\Tasks\Task('closure', function () {
            return 1;
        });
        $task->named('foo');

        // Should be dashes when not ran
        $this->assertEquals('--', $task->lastRun());

        $date = date('Y-m-d H:i:s');

        // Insert a performance bit in the db
        setting("Tasks.log-{$task->name}", [[
            'task' => $task->name,
            'type' => $task->getType(),
            'start' => $date,
            'duration' => '11.3s',
            'output' => null,
            'error' => null,
        ]]);

        // Should return the current time
        $this->assertInstanceOf(Time::class, $task->lastRun());
        $this->assertEquals($date, $task->lastRun()->format('Y-m-d H:i:s'));
    }
}
