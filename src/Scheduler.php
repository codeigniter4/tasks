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

namespace CodeIgniter\Tasks;

use Closure;

class Scheduler
{
    protected array $tasks = [];

    /**
     * Returns the created Tasks.
     *
     * @return list<Task>
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    // --------------------------------------------------------------------

    /**
     * Schedules a closure to run.
     */
    public function call(Closure $func): Task
    {
        return $this->createTask('closure', $func);
    }

    /**
     * Schedules a console command to run.
     */
    public function command(string $command): Task
    {
        return $this->createTask('command', $command);
    }

    /**
     * Schedules a local function to be exec'd
     */
    public function shell(string $command): Task
    {
        return $this->createTask('shell', $command);
    }

    /**
     * Schedules an Event to trigger
     *
     * @param string $name Name of the event to trigger
     */
    public function event(string $name): Task
    {
        return $this->createTask('event', $name);
    }

    /**
     * Schedules a cURL command to a remote URL
     */
    public function url(string $url): Task
    {
        return $this->createTask('url', $url);
    }

    // --------------------------------------------------------------------

    /**
     * @param mixed $action
     */
    protected function createTask(string $type, $action): Task
    {
        $task          = new Task($type, $action);
        $this->tasks[] = $task;

        return $task;
    }
}
