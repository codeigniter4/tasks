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
     * @return array
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
