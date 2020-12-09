<?php namespace CodeIgniter\Tasks;

use CodeIgniter\CLI\CLI;

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

		if (! count($tasks))
		{
			return;
		}

		try
		{
			foreach ($tasks as $task)
			{
				if ($task->shouldRun())
				{
					$task->run();
				}
			}
		}
		catch (\Throwable $e)
		{
			log_message('error', $e->getMessage(), $e->getTrace());
		}
	}
}
