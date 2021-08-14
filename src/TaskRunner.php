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
     * Stores aliases of tasks to run
     * If empty, All tasks will be executed as per their schedule
     *
     * @var array
     */
    protected $only = [];

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

        if (! count($tasks)) {
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
     * Write a line to command line interface
     *
     * @param string      $text
     * @param string|null $foreground
     */
    protected function cliWrite(string $text, string $foreground = null)
    {
        // Skip writing to cli in tests
        if (defined("ENVIRONMENT") && ENVIRONMENT === "testing") {
            return ;
        }

        if (! is_cli()) {
            return ;
        }

        CLI::write("[" . date("Y-m-d H:i:s") . "] " . $text, $foreground);
    }

    /**
     * Adds the performance log to the
     *
     * @param TaskLog $taskLog
     */
    protected function updateLogs(TaskLog $taskLog)
    {
        if (setting('Tasks.logPerformance') === false) {
            return;
        }

        // Build a name if the task doesn't exist
        $name = $taskLog->task->name ?? $this->buildName($taskLog->task);

        $data = [
            'task' => $name,
            'type' => $taskLog->task->getType(),
            'duration' => $taskLog->duration(),
            'output' => $taskLog->output ?? null,
            'error' => serialize($taskLog->error ?? null),
        ];

        // Get existing logs
        $logs = setting("Tasks.log-{$name}");
        if (empty($logs)) {
            $logs = [];
        }

        // Make sure we have room for one more
        if (count($logs) > setting('Tasks.maxLogsPerTask')) {
            array_pop($logs);
        }

        // Add the log to the top of the array
        array_unshift($logs, $data);

        setting("Tasks.log-{$name}", $logs);
    }

    private function buildName(Task $task)
    {
        // Get a hash based on the action
        // Closures cannot be serialized so do it the hard way
        if ($task->getType() === 'closure') {
            $ref  = new \ReflectionFunction($task->getAction());
            $file = new \SplFileObject($ref->getFileName());
            $file->seek($ref->getStartLine()-1);
            $content = '';
            while ($file->key() < $ref->getEndLine()) {
                $content .= $file->current();
                $file->next();
            }
            $actionString = json_encode(array(
                $content,
                $ref->getStaticVariables()
            ));
        } else {
            $actionString = serialize($task->getAction());
        }

        // Get a hash based on the expression
        $expHash = $task->getExpression();

        return  $task->getType() .'_'. md5($actionString .'_'. $expHash);
    }
}
