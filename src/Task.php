<?php namespace CodeIgniter\Tasks;

/**
 * Class Task
 *
 * Represents a single task that should be scheduled
 * and ran periodically.
 *
 * @package CodeIgniter\Tasks
 */
class Task
{
    /**
     * The command, shell command, or Closure
     * that should be ran.
     *
     * @var mixed
     */
    protected $task;

    /**
     * The task type, either 'callable', 'command', or 'shell'
     *
     * @var string
     */
    protected $taskType;

    /**
     * The crontab expression for this task
     *
     * @var string
     */
    protected $cronExpression = '* * * * *';

    public function __construct($task, string $taskType)
    {
        $this->task = $task;
        $this->taskType = $taskType;
    }

    public function run()
    {

    }

    /**
     * Schedules the task through a raw crontab expression string.
     *
     * @param string $expression
     *
     * @return $this
     */
    public function cron(string $expression)
    {
        $this->cronExpression = $expression;

        return $this;
    }
}
