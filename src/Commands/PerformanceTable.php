<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\GeneratorTrait;
use CodeIgniter\Services;

/**
 * Generates a Tasks' Perforamance Table Migration file.
 */
class PerformanceTable extends TaskCommand
{
	use GeneratorTrait;

	/**
	 * The Command's Name
	 *
	 * @var string
	 */
	protected $name = 'tasks:performance-table';

	/**
	 * The Command's Description
	 *
	 * @var string
	 */
	protected $description = 'Generates a tasks\' performance migration file.';

	/**
	 * The Command's Usage
	 *
	 * @var string
	 */
	protected $usage = 'tasks:performance-table';

	/**
	 * Actually execute a command.
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{
		$this->component = 'Migration';
		$this->directory = 'Database\Migrations';
		$this->template  = 'performance_table.tpl.php';

		$params[0] = "_create_performance_tasks_table";

		$this->execute($params);
	}


	/**
	 * Change file basename before saving.
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	protected function basename($filename): string
	{
		// Set a unqiue name to avoid writing more than one migration file
		return "2021-01-01-000000_" . basename($filename);
	}


	/**
	 * Gets the generator view as defined in the `Config\Generators::$views`,
	 * with fallback to `$template` when the defined view does not exist.
	 *
	 * @param array $data Data to be passed to the view.
	 *
	 * @return string
	 */
	protected function renderTemplate(array $data = []): string
	{
		$content = file_get_contents(__DIR__ . "/Views/".$this->template);

		return service("renderer")
		               ->setData($data, 'raw')
		               ->renderString($content);
	}

}
