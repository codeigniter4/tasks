<?php namespace CodeIgniter\Tasks\Config;

use _HumbugBoxe5640220fe34\Nette\Neon\Exception;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseConfig
{
	/**
	 * Register any tasks within this method for the application.
	 * Called by the TaskRunner.
	 *
	 * @param Scheduler $schedule
	 */
	public function init(Scheduler $schedule)
	{
		// $schedule->command('foo:bar')->nightly();

		// $schedule->shell('cp foo bar')->daily()->at('11:00 pm');

		$schedule->call(function() {
			//echo "HHH";
			sleep(2);
			usleep(500);
			file_put_contents("test.txt", time());
			return "This Is The Output";
		})
		         ->enablePerformance()
		         ->everyMinute(5)
		         ->named('foo');

		$schedule->call(function() {
			//echo "HHH";
			throw new Exception("There is an error");
			//return "This Is The Output";
		})
		         ->enablePerformance()
		         ->everyMinute(8)
		         ->named('bar');
	}
}
