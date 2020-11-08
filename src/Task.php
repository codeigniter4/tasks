<?php namespace CodeIgniter\Tasks;

/**
 * Class Task
 *
 * Represents a single task that should be scheduled
 * and run periodically.
 */
class Task
{
	use FrequenciesTrait;

	/**
	 * The actual content that should be run.
	 *
	 * @var mixed
	 */
	protected $action;

	/**
	 * The type of action.
	 * Enum 'callable', 'command', or 'shell'
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The timezone this should be evaluated in.
	 *
	 * @var string
	 *
	 * @todo Needs to be implemented
	 */
	protected $timezone;

	/**
	 * If not empty, lists the allowed environments
	 * this can run in.
	 *
	 * @var string[]
	 */
	protected $environments = [];

	/**
	 * @param mixed  $task
	 * @param string $taskType
	 */
	public function __construct($task, string $taskType)
	{
		$this->task     = $task;
		$this->taskType = $taskType;
	}

	/**
	 * Runs this Task's action.
	 *
	 * @todo
	 */
	public function run()
	{
	}

	/**
	 * Determines whether this task should be run now
	 * according to its schedule, timezone, and environment.
	 *
	 * @return boolean
	 */
	public function shouldRun(): bool
	{
	}

	/**
	 * Returns the saved action.
	 *
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Returns the type.
	 *
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * Restricts this task to run within only
	 * specified environements.
	 *
	 * @param mixed ...$environments
	 *
	 * @return $this
	 */
	protected function environments(...$environments)
	{
		$this->environments = $environments;

		return $this;
	}

	/**
	 * Checks if it runs within the specified environment.
	 *
	 * @param string $environment
	 *
	 * @return boolean
	 */
	protected function runsInEnvironment(string $environment): bool
	{
		// If nothing is specified then it should run
		if (empty($this->environments))
		{
			return true;
		}

		return in_array($environment, $this->environments);
	}
}
