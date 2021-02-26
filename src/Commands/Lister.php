<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use CodeIgniter\Tasks\TaskRunner;

/**
 * Lists currently scheduled tasks.
 */
class Lister extends BaseCommand
{
	use Settings;

	/**
	 * Command grouping.
	 *
	 * @var string
	 */
	protected $group = 'Tasks';

	/**
	 * The Command's name
	 *
	 * @var string
	 */
	protected $name = 'tasks:list';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'Lists the tasks currently set to run.';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'tasks:list';

	/**
	 * the Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [
		//'driver' => 'The cache driver to use',
	];

	/**
	 * Lists upcoming tasks
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{
		$settings = $this->getSettings();

		if ($settings['status'] !== 'enabled')
		{
			CLI::write('WARNING: Task running is currently disabled.', 'red');
			CLI::write('To re-enable tasks run: tasks:enable');
		}

		$scheduler = \Config\Services::scheduler();

		config('Tasks')->init($scheduler);

		$runner = new TaskRunner();

		$tasks = [];

		foreach ($scheduler->getTasks() as $task)
		{

			$cron = service('cronExpression');

			$nextRun = $cron->nextRun($task->getExpression());

			$tasks[] = [
				'name'     => $task->name ?: $task->getAction(),
				'type'     => $task->getType(),
				'next_run' => $nextRun,
				'runs_in'  => $nextRun->humanize(),
			];

		endforeach;

		usort($tasks, function ($a, $b) {
				return ($a['next_run'] < $b['next_run']) ? -1 : 1;
		});

		CLI::table($tasks, [
					   'Name',
					   'Type',
					   'Next Run',
					   '',
				   ]);
	}
}
