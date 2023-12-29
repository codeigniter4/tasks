<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Tasks.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
