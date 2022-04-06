<?php

namespace CodeIgniter\Tasks\Test;

use CodeIgniter\Tasks\Scheduler;
use CodeIgniter\Tasks\Task;

/**
 * Mock Scheduler Class
 *
 * A wrapper class for testing to return
 * MockTasks instead of Tasks.
 */
class MockScheduler extends Scheduler
{
    /**
     * @param mixed $action
     *
     * @return MockTask
     */
    protected function createTask(string $type, $action): Task
    {
        $task          = new MockTask($type, $action);
        $this->tasks[] = $task;

        return $task;
    }
}
