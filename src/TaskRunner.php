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

use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;
use Throwable;

/**
 * Class TaskRunner
 */
class TaskRunner
{
    protected Scheduler $scheduler;
    protected ?string $testTime = null;

    /**
     * Stores aliases of tasks to run
     * If empty, All tasks will be executed as per their schedule
     */
    protected array $only = [];

    public function __construct()
    {
        helper('setting');
        $this->scheduler = service('scheduler');
    }

    /**
     * The main entry point to run tasks within the system.
     * Also handles collecting output and sending out
     * notifications as necessary.
     */
    public function run()
    {
        $tasks = $this->scheduler->getTasks();

        if ($tasks === []) {
            return;
        }

        foreach ($tasks as $task) {
            // If specific tasks were chosen then skip executing remaining tasks
            if (! empty($this->only) && ! in_array($task->name, $this->only, true)) {
                continue;
            }

            if (! $task->shouldRun($this->testTime) && empty($this->only)) {
                continue;
            }

            $error  = null;
            $start  = Time::now();
            $output = null;

            $this->cliWrite('Processing: ' . ($task->name ?: 'Task'), 'green');

            try {
                $output = $task->run();

                $this->cliWrite('Executed: ' . ($task->name ?: 'Task'), 'cyan');
            } catch (Throwable $e) {
                $this->cliWrite('Failed: ' . ($task->name ?: 'Task'), 'red');

                log_message('error', $e->getMessage(), $e->getTrace());
                $error = $e;
            } finally {
                // Save performance info
                $taskLog = new TaskLog([
                    'task'     => $task,
                    'output'   => $output,
                    'runStart' => $start,
                    'runEnd'   => Time::now(),
                    'error'    => $error,
                ]);

                $this->updateLogs($taskLog);
            }
        }
    }

    /**
     * Specify tasks to run
     */
    public function only(array $tasks = []): TaskRunner
    {
        $this->only = $tasks;

        return $this;
    }

    /**
     * Sets a time that will be used.
     * Allows setting a specific time to test against.
     * Must be in a DateTime-compatible format.
     */
    public function withTestTime(string $time): TaskRunner
    {
        $this->testTime = $time;

        return $this;
    }

    /**
     * Write a line to command line interface
     */
    protected function cliWrite(string $text, ?string $foreground = null)
    {
        // Skip writing to cli in tests
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'testing') {
            return;
        }

        if (! is_cli()) {
            return;
        }

        CLI::write('[' . date('Y-m-d H:i:s') . '] ' . $text, $foreground);
    }

    /**
     * Adds the performance log to the
     */
    protected function updateLogs(TaskLog $taskLog)
    {
        if (setting('Tasks.logPerformance') === false) {
            return;
        }

        // "unique" name will be returned if one wasn't set
        $name = $taskLog->task->name;

        $data = [
            'task'     => $name,
            'type'     => $taskLog->task->getType(),
            'start'    => $taskLog->runStart->format('Y-m-d H:i:s'),
            'duration' => $taskLog->duration(),
            'output'   => $taskLog->output ?? null,
            'error'    => serialize($taskLog->error ?? null),
        ];

        // Get existing logs
        $logs = setting("Tasks.log-{$name}");
        if (empty($logs)) {
            $logs = [];
        }

        // Make sure we have room for one more
        /** @var int $maxLogsPerTask */
        $maxLogsPerTask = setting('Tasks.maxLogsPerTask');
        if ((is_countable($logs) ? count($logs) : 0) > $maxLogsPerTask) {
            array_pop($logs);
        }

        // Add the log to the top of the array
        array_unshift($logs, $data);

        setting("Tasks.log-{$name}", $logs);
    }
}
