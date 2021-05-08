<?php namespace CodeIgniter\Tasks;

use CodeIgniter\Events\Events;
use CodeIgniter\Tasks\Exceptions\TasksException;
use Config\Services;
use DateTime;

/**
 * Class Task
 *
 * Represents a single task that should be scheduled
 * and run periodically.
 *
 * @property-read array       $types
 * @property-read string      $type
 * @property-read mixed       $action
 * @property-read array       $environments
 * @property-read string      $connection
 * @property-read bool        $performance
 * @property-read string|null $name
 */
class Task
{
	use FrequenciesTrait;

	/**
	 * Supported action types.
	 *
	 * @var string[]
	 */
	protected $types = [
		'command',
		'shell',
		'closure',
		'event',
		'url',
	];

	/**
	 * The type of action.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The actual content that should be run.
	 *
	 * @var mixed
	 */
	protected $action;

	/**
	 * If not empty, lists the allowed environments
	 * this can run in.
	 *
	 * @var array
	 */
	protected $environments = [];

	/**
	 * The alias this task can be run by
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Enable performance tracking
	 *
	 * @var bool
	 */
	protected $performance = false;

	/**
	 * Database Connection for Performance Table
	 *
	 * @var mixed
	 */
	protected $connection = "default";

	/**
	 * @param mixed  $action
	 * @param string $type
	 *
	 * @throws TasksException
	 */
	public function __construct(string $type, $action)
	{
		if (!in_array($type, $this->types))
		{
			throw TasksException::forInvalidTaskType($type);
		}

		$this->type = $type;
		$this->action = $action;
	}

	/**
	 * Set the name to reference this task by
	 *
	 * @param string $name
	 *
	 * @return $this
	 */
	public function named(string $name)
	{
		$this->name = $name;

		return $this;
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
	 * Returns the saved action.
	 *
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Runs this Task's action.
	 *
	 * @throws TasksException
	 */
	public function run()
	{
		$method = 'run' . ucfirst($this->type);
		if (!method_exists($this, $method))
		{
			throw TasksException::forInvalidTaskType($this->type);
		}

		return $this->$method();
	}

	/**
	 * Determines whether this task should be run now
	 * according to its schedule and environment.
	 *
	 * @param string|null $testTime
	 *
	 * @return boolean
	 */
	public function shouldRun(string $testTime = null): bool
	{
		$cron = new \Cron\CronExpression($this->getExpression());

		// Are we restricting to environments?
		if (!empty($this->environments) && !$this->runsInEnvironment($_SERVER['CI_ENVIRONMENT']))
		{
			return false;
		}

		return $cron->isDue(empty($testTime) ? 'now' : $testTime);
	}

	/**
	 * Restricts this task to run within only
	 * specified environements.
	 *
	 * @param mixed ...$environments
	 *
	 * @return $this
	 */
	public function environments(...$environments)
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

	/**
	 * Runs a framework Command.
	 *
	 * @return string Buffered output from the Command
	 * @throws \InvalidArgumentException
	 */
	protected function runCommand(): string
	{
		return command($this->getAction());
	}

	/**
	 * Executes a shell script.
	 *
	 * @return array Lines of output from exec
	 */
	protected function runShell(): array
	{
		exec($this->getAction(), $output);

		return $output;
	}

	/**
	 * Calls a Closure.
	 *
	 * @return mixed The result of the closure
	 */
	protected function runClosure()
	{
		return $this->getAction()->__invoke();
	}

	/**
	 * Triggers an Event.
	 *
	 * @return boolean Result of the trigger
	 */
	protected function runEvent(): bool
	{
		return Events::trigger($this->getAction());
	}

	/**
	 * Queries a URL.
	 *
	 * @return mixed|string Body of the Response
	 */
	protected function runUrl()
	{
		$response = Services::curlrequest()->request('GET', $this->getAction());

		return $response->getBody();
	}

	/**
	 * Enable performance data to be saved
	 *
	 * @return $this
	 */
	public function enablePerformance(): Task
	{
		$this->performance = true;
		return $this;
	}

	/**
	 * Select database connection for performance data
	 *
	 * @param string $databaseConnection
	 * @return $this
	 */
	public function onConnection(string $databaseConnection): Task
	{
		$this->connection = $databaseConnection;
		return $this;
	}

	/**
	 * Get the time for the next run for this task
	 *
	 * @param string|null $testTime
	 * @return \DateTime
	 */
	public function nextRun(string $testTime = null) : DateTime
	{
		$cron = new \Cron\CronExpression($this->getExpression());
		return $cron->getNextRunDate( empty($testTime) ? 'now' : $testTime );
	}

	/**
	 * Magic getter
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get(string $key)
	{
		if (property_exists($this, $key))
		{
			return $this->$key;
		}
	}
}
