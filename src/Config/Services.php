<?php namespace CodeIgniter\Tasks\Config;

use Config\Services as BaseServices;
use CodeIgniter\Tasks\Scheduler;

class Services extends BaseServices
{
	/**
	 * Returns the Task Scheduler
	 *
	 * @return Scheduler
	 */
	public static function scheduler(bool $getShared = true): Scheduler
	{
		if ($getShared)
		{
			return static::getSharedInstance('scheduler');
		}

		return new Scheduler();
	}
}
