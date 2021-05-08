<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use CodeIgniter\Tasks\TaskRunner;

/**
 * Enables Task Running
 */
class Install extends TaskCommand
{
	/**
	 * The Command's name
	 *
	 * @var string
	 */
	protected $name = 'tasks:install';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'install tasks dependancies.';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'tasks:install';

	/**
	 * Enables task running
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{
		// Todo: compatiblity with Linux should be added

		// Temporary we will use a relative path instead of ROOTPATH
		$ROOTPATH = realpath(__DIR__ . "/../../");

		if (!is_file($ROOTPATH . "\composer.phar"))
		{
			// Download Composer
			CLI::write('Downloading Composer...', 'green');

			copy('https://getcomposer.org/installer', $ROOTPATH . '\composer-setup.php');

			exec('cd "' . $ROOTPATH . '\" && php "' . $ROOTPATH . '\composer-setup.php" --src "' . $ROOTPATH . '\"', $output);

			if (strpos(implode("", $output), "successfully installed") === false)
			{
				CLI::write('Failed to install composer !', 'red');
				return;
			}

			unlink($ROOTPATH . "\composer-setup.php");

			CLI::write('Composer is installed.', 'cyan');
		}

		if (is_file(COMPOSER_PATH))
		{
			$composer = include COMPOSER_PATH;

			if (array_key_exists("Cron\\", $composer->getPrefixesPsr4()))
			{
				CLI::write('Dependencies are already installed.', 'cyan');

				return;
			}
		}

		CLI::write('Installing Dependencies...', 'green');

		exec('cd "' . $ROOTPATH . '\" && php "' . $ROOTPATH . '\composer.phar" -- require dragonmantank/cron-expression');

		CLI::write('Done.', 'green');
	}
}
