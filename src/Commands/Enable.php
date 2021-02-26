<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use CodeIgniter\Tasks\TaskRunner;

/**
 * Enables Task Running
 */
class Enable extends BaseCommand
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
	protected $name = 'tasks:enable';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'Enables the task runner.';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'tasks:enable';

	/**
	 * the Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [
		//'driver' => 'The cache driver to use',
	];

	/**
	 * Enables task running
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{
		$this->saveSettings('enabled');

		CLI::write(CLI::color('Tasks have been enabled.', 'green'));
	}
}
