<?php

namespace CodeIgniter\Tasks\Test;

use CodeIgniter\Tasks\Exceptions\TasksException;
use CodeIgniter\Tasks\Task;

/**
 * Mock Task Class
 *
 * Test class that prevents actions
 * from being called.
 */
class MockTask extends Task
{
    /**
     * Pretends to run this Task's action.
     *
     * @throws TasksException
     */
    public function run()
    {
        $method = 'run' . ucfirst($this->type);
        if (! method_exists($this, $method)) {
            throw TasksException::forInvalidTaskType($this->type);
        }

        $_SESSION['tasks_cache'] = [$this->type, $this->action];

        return [
            'command' => 'success',
            'shell'   => [],
            'closure' => 42,
            'event'   => true,
            'url'     => 'body',
        ][$this->type];
    }
}
