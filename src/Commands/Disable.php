<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use CodeIgniter\Tasks\TaskRunner;

/**
 * Disable Task Running.
 */
class Disable extends BaseCommand
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
	protected $name = 'tasks:disable';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'Disables the task runner.';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'tasks:disable';

	/**
	 * the Command's Arguments
	 *
	 * @var array
	 */
	protected $arguments = [
		//'driver' => 'The cache driver to use',
	];

	/**
	 * Dsiables task running
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{
		$this->saveSettings('disabled');

		CLI::write(CLI::color('Tasks have been disabled.', 'red'));
	}
}
