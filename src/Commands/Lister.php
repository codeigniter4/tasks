<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use CodeIgniter\I18n\Time;
use CodeIgniter\Tasks\TaskRunner;

/**
 * Lists currently scheduled tasks.
 */
class Lister extends TaskCommand
{
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

		$tasks = [];

		foreach ($scheduler->getTasks() as $task)
		{
			$nextRun = Time::createFromInstance($task->nextRun());

			$tasks[] = [
				'name'     => $task->name ?: $task->getAction(),
				'type'     => $task->getType(),
				'last_run' => "-",          // todo: will be done later
				'next_run' => $nextRun,
				'runs_in'  => $nextRun->humanize(),
			];
		}

		usort($tasks, function ($a, $b) {
				return ($a['next_run'] < $b['next_run']) ? -1 : 1;
		});

		CLI::table($tasks, [
			'Name',
			'Type',
			'Last Run',
			'Next Run',
			'',
		]);
	}
}
