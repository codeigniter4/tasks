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
	 * Schedules an Event to trigger
	 *
	 * @param string $name  Name of the event to trigger
	 * @param array $params Optional parameters for the Event
	 */
	public function event(string $name, array $params = [])
	{
	}

	/**
	 * Schedules a cURL command to a remote URL
	 *
	 * @param string $url
	 */
	public function url(string $url)
	{
	}

	//--------------------------------------------------------------------

	/**
	 * @param mixed  $command
	 * @param string $type
	 *
	 * @return Task
	 */
	protected function createTask($command, string $type)
	{
		$task          = new Task($command, $type);
		$this->tasks[] = $task;

		return $task;
	}
}
