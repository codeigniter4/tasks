<?php

use CodeIgniter\Tasks\Task;
use CodeIgniter\Tasks\TaskRunner;
use CodeIgniter\Test\CIUnitTestCase as TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Services;

/**
 * @internal
 */
final class TaskRunnerTest extends TestCase
{
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace;

    protected function setUp(): void
    {
        parent::setUp();

        helper('setting');
        setting('Tasks.logPerformance', true);
    }

    public function testRunWithNoTasks()
    {
        $this->assertNull($this->getRunner()->run());
    }

    public function testRunWithSuccess()
    {
        $task1 = (new Task('closure', static function () {
            echo 'Task 1';
        }))->daily('12:05am')->named('task1');
        $task2 = (new Task('closure', static function () {
            echo 'Task 2';
        }))->daily('12:00am')->named('task2');

        $runner = $this->getRunner([$task1, $task2]);

        ob_start();

        $runner->withTestTime('12:00am')
            ->run();

        // Only task 2 should have ran
        $this->assertSame('Task 2', $this->getActualOutput());

        ob_end_clean();

        // Should have logged the stats
        $expected = [
            [
                'task'     => 'task2',
                'type'     => 'closure',
                'start'    => date('Y-m-d H:i:s'),
                'duration' => '0.00',
                'output'   => null,
                'error'    => serialize(null),
            ],
        ];
        $this->seeInDatabase('settings', [
            'class' => 'CodeIgniter\Tasks\Config\Tasks',
            'key'   => 'log-task2',
            'value' => serialize($expected),
        ]);
        $this->dontSeeInDatabase('settings', [
            'key' => 'log-task1',
        ]);
    }

    protected function getRunner(array $tasks = [])
    {
        $scheduler = service('scheduler');
        $this->setPrivateProperty($scheduler, 'tasks', $tasks);
        Services::injectMock('scheduler', $scheduler);

        return new TaskRunner();
    }
}
