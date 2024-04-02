<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Tasks.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Tasks;

use CodeIgniter\Events\Events;
use CodeIgniter\I18n\Time;
use CodeIgniter\Tasks\Exceptions\TasksException;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use SplFileObject;

/**
 * Class Task
 *
 * Represents a single task that should be scheduled
 * and run periodically.
 *
 * @property mixed  $action
 * @property array  $environments
 * @property string $name
 * @property string $type
 * @property array  $types
 */
class Task
{
    use FrequenciesTrait;

    /**
     * Supported action types.
     *
     * @var list<string>
     */
    protected array $types = [
        'command',
        'shell',
        'closure',
        'event',
        'url',
    ];

    /**
     * The type of action.
     */
    protected string $type;

    /**
     * The actual content that should be run.
     *
     * @var mixed
     */
    protected $action;

    /**
     * If not empty, lists the allowed environments
     * this can run in.
     */
    protected array $environments = [];

    /**
     * The alias this task can be run by
     */
    protected string $name;

    /**
     * @param mixed $action
     *
     * @throws TasksException
     */
    public function __construct(string $type, $action)
    {
        if (! in_array($type, $this->types, true)) {
            throw TasksException::forInvalidTaskType($type);
        }

        $this->type   = $type;
        $this->action = $action;
    }

    /**
     * Set the name to reference this task by
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
     * @return mixed
     *
     * @throws TasksException
     */
    public function run()
    {
        $method = 'run' . ucfirst($this->type);
        if (! method_exists($this, $method)) {
            throw TasksException::forInvalidTaskType($this->type);
        }

        return $this->{$method}();
    }

    /**
     * Determines whether this task should be run now
     * according to its schedule and environment.
     */
    public function shouldRun(?string $testTime = null): bool
    {
        $cron = service('cronExpression');

        // Allow times to be set during testing
        if (! empty($testTime)) {
            $cron->testTime($testTime);
        }

        // Are we restricting to environments?
        if (! empty($this->environments) && ! $this->runsInEnvironment($_SERVER['CI_ENVIRONMENT'])) {
            return false;
        }

        return $cron->shouldRun($this->getExpression());
    }

    /**
     * Restricts this task to run within only
     * specified environments.
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
     * Returns the date this was last ran.
     *
     * @return string|Time
     */
    public function lastRun()
    {
        helper('setting');
        if (setting('Tasks.logPerformance') === false) {
            return '--';
        }

        // Get the logs
        $logs = setting("Tasks.log-{$this->name}");

        if (empty($logs)) {
            return '--';
        }

        $log = array_shift($logs);

        return Time::parse($log['start']);
    }

    /**
     * Checks if it runs within the specified environment.
     */
    protected function runsInEnvironment(string $environment): bool
    {
        // If nothing is specified then it should run
        if (empty($this->environments)) {
            return true;
        }

        return in_array($environment, $this->environments, true);
    }

    /**
     * Runs a framework Command.
     *
     * @return string Buffered output from the Command
     *
     * @throws InvalidArgumentException
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
        $response = service('curlrequest')->request('GET', $this->getAction());

        return $response->getBody();
    }

    /**
     * Builds a unique name for the task.
     * Used when an existing name doesn't exist.
     *
     * @return string
     *
     * @throws ReflectionException
     */
    protected function buildName()
    {
        // Get a hash based on the action
        // Closures cannot be serialized so do it the hard way
        if ($this->getType() === 'closure') {
            $ref  = new ReflectionFunction($this->getAction());
            $file = new SplFileObject($ref->getFileName());
            $file->seek($ref->getStartLine() - 1);
            $content = '';

            while ($file->key() < $ref->getEndLine()) {
                $content .= $file->current();
                $file->next();
            }
            $actionString = json_encode([
                $content,
                $ref->getStaticVariables(),
            ]);
        } else {
            $actionString = serialize($this->getAction());
        }

        // Get a hash based on the expression
        $expHash = $this->getExpression();

        return $this->getType() . '_' . md5($actionString . '_' . $expHash);
    }

    /**
     * Magic getter
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if ($key === 'name' && empty($this->name)) {
            return $this->buildName();
        }

        if (property_exists($this, $key)) {
            return $this->{$key};
        }
    }
}
