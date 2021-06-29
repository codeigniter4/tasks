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
            if (! $task->shouldRun($this->testTime)) {
                continue;
            }

            $error  = null;
            $start  = Time::now();
            $output = null;

            try {
                $output = $task->run();
            } catch (\Throwable $e) {
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
     * Sets a time that will be used.
     * Allows setting a specific time to test against.
     * Must be in a DateTime-compatible format.
     *
     * @param string $time
     *
     * @return $this
     */
    public function withTestTime(string $time)
    {
        $this->testTime = $time;

        return $this;
    }

    /**
     * Returns the performance logs, if any.
     *
     * @return array
     */
    public function performanceLogs()
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
