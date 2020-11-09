<?php namespace CodeIgniter\Tasks;

use CodeIgniter\Events\Events;
use CodeIgniter\Tasks\Exceptions\TasksException;
use Config\Services;

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
	 * Supported action types.
	 *
	 * @var string[]
	 */
	protected $types = ['command', 'shell', 'closure', 'event', 'url'];

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
	 * @param mixed  $action
	 * @param string $type
	 *
	 * @throws TasksException
	 */
	public function __construct(string $type, $action)
	{
		if (! in_array($type, $this->types))
		{
			throw TasksException::forInvalidTaskType($type);
		}

		$this->type   = $type;
		$this->action = $action;
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

	//--------------------------------------------------------------------

	/**
	 * Runs this Task's action.
	 *
	 * @throws TasksException
	 */
	public function run()
	{
		$method = 'run' . ucfirst($this->type);
		if (! method_exists($this, $method))
		{
			throw TasksException::forInvalidTaskType($this->type);
		}

		return $this->$method();
	}

	/**
	 * Determines whether this task should be run now
	 * according to its schedule, timezone, and environment.
	 *
	 * @return boolean
	 */
	public function shouldRun(): bool
	{
		/** @todo */
		return true;
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

	//--------------------------------------------------------------------

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
	 * @return bool Result of the trigger
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
}
