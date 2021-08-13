<?php

namespace CodeIgniter\Tasks;

use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

/**
 * Class TaskRunner
 *
 * @package CodeIgniter\Tasks
 */
class TaskRunner
{
    /**
     * @var Scheduler
     */
    protected $scheduler;

    /**
     * @var string
     */
    protected $testTime;

    /**
     * Stores execution logs for each
     * task that was ran
     *
     * @var array
     */
    protected $performanceLogs = [];

    /**
     * Stores aliases of tasks to run
     * If empty, All tasks will be executed as per their schedule
     *
     * @var array
     */
    protected $only = [];

    public function __construct()
    {
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

        if (! count($tasks)) {
            return;
        }

        foreach ($tasks as $task) {
            // If specific tasks were chosen then skip executing remaining tasks
            if(! empty($this->only) && ! in_array($task->name, $this->only)) {
                continue;
            }

            if (! $task->shouldRun($this->testTime) && empty($this->only)) {
                continue;
            }

            $error  = null;
            $start  = Time::now();
            $output = null;

            $this->cliWrite("Processing: " . ($task->name ?: "Task"), 'green');

            try {
                $output = $task->run();

                $this->cliWrite("Executed: " . ($task->name ?: "Task"), "cyan");
            } catch (\Throwable $e) {
                $this->cliWrite("Failed: " . ($task->name ?: "Task"), "red");

                log_message('error', $e->getMessage(), $e->getTrace());
                $error = $e;
            } finally {
                // Save performance info
                $this->performanceLogs[] = new TaskLog([
                    'task'     => $task,
                    'output'   => $output,
                    'runStart' => $start,
                    'runEnd'   => Time::now(),
                    'error'    => $error,
                ]);
            }
        }
    }

    /**
     * Write a line to command line interface
     *
     * @param string      $text
     * @param string|null $foreground
     */
    protected function cliWrite(string $text, string $foreground = null)
    {
        // Skip writing to cli in tests
        if(defined("ENVIRONMENT") && ENVIRONMENT === "testing") {
            return ;
        }

        if(! is_cli()) {
            return ;
        }

        CLI::write("[" . date("Y-m-d H:i:s") . "] " . $text, $foreground);
    }

    /**
     * Specify tasks to run
     *
     * @param array $tasks
     *
     * @return TaskRunner
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
     *
     * @param string $time
     *
     * @return $this
     */
    public function withTestTime(string $time): TaskRunner
    {
        $this->testTime = $time;

        return $this;
    }

    /**
     * Returns the performance logs, if any.
     *
     * @return array
     */
    public function performanceLogs(): array
    {
        return $this->performanceLogs;
    }

    /**
     * Performance log information is stored
     * at /writable/tasks/tasks_yyyy_mm_dd.json
     */
    protected function storePerformanceLogs()
    {
        if (empty($this->performanceLogs)) {
            return;
        }

        // Ensure we have someplace to store the log
        if (! is_dir(WRITEPATH . 'tasks')) {
            mkdir(WRITEPATH . 'tasks', 0777);
        }

        $fileName = 'tasks_' . date('Y_m_d') . '.json';

        dd($fileName);
    }
}
