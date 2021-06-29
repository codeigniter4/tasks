<?php

use CodeIgniter\Tasks\Task;
use CodeIgniter\Tasks\Scheduler;
use CodeIgniter\Tasks\TaskRunner;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

class TaskRunnerTest extends TestCase
{
    public function testRunWithNoTasks()
    {
        $this->assertNull($this->getRunner()->run());
    }

    public function testRunWithSuccess()
    {
        $task1 = (new Task('closure', function () {
            echo 'Task 1';
        }))->daily('12:05am');
        $task2 = (new Task('closure', function () {
            echo 'Task 2';
        }))->daily('12:00am');

        $runner = $this->getRunner([$task1, $task2]);

        ob_start();

        $runner->withTestTime('12:00am')
            ->run();

        // Only task 2 should have ran
        $this->assertEquals('Task 2', $this->getActualOutput());

        ob_end_clean();

        // Should have logged the stats
        $logs = $runner->performanceLogs();
        $this->assertCount(1, $logs);
    }

    protected function getRunner(array $tasks = [])
    {
        $scheduler = service('scheduler');
        $this->setPrivateProperty($scheduler, 'tasks', $tasks);
        \Config\Services::injectMock('scheduler', $scheduler);

        $runner = new TaskRunner();

        return $runner;
    }
}
