<?php

use CodeIgniter\Tasks\Task;
use CodeIgniter\Tasks\Scheduler;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

class SchedulerTest extends TestCase
{
    /**
     * @var Scheduler
     */
    protected $scheduler;

    public function setUp(): void
    {
        parent::setUp();

        $this->scheduler = new Scheduler();
    }

    public function testCallSavesTask()
    {
        $function = function() {
            return "Hello";
        };

        $task = $this->scheduler->call($function);

        $this->assertInstanceOf(\Closure::class, $function);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($function, $task->getTask());
        $this->assertEquals('Hello', $task->getTask()());
    }

    public function testCommandSavesTask()
    {
        $task = $this->scheduler->command('foo:bar');

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('foo:bar', $task->getTask());
    }

    public function testShellSavesTask()
    {
        $task = $this->scheduler->shell('foo:bar');

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('foo:bar', $task->getTask());
    }
}
