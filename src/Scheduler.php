<?php namespace CodeIgniter\Tasks;

class Scheduler
{
    /**
     * @var array
     */
    protected $tasks = [];

    /**
     * Schedules a closure to run.
     *
     * @param \Closure $func
     */
    public function call(\Closure $func)
    {
        return $this->createTask($func, 'closure');
    }

    /**
     * Schedules a console command to run.
     *
     * @param string $command
     */
    public function command(string $command)
    {
        return $this->createTask($command, 'command');
    }

    /**
     * Schedules a local function to be exec'd
     *
     * @param string $command
     */
    public function shell(string $command)
    {
        return $this->createTask($command, 'shell');
    }

    /**
     * @param        $command
     * @param string $type
     *
     * @return Task
     */
    protected function createTask($command, string $type)
    {
        $task = new Task($command, $type);
        $this->tasks[] = $task;

        return $task;
    }
}
