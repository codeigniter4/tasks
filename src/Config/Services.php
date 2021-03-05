<?php namespace CodeIgniter\Tasks\Config;

use CodeIgniter\Tasks\CronExpression;
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

	/**
	 * Returns the CronExpression class.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Tasks\CronExpression
	 */
	public static function cronExpression(bool $getShared = true): CronExpression
	{
		if ($getShared)
		{
			return static::getSharedInstance('cronExpression');
		}

		return new CronExpression();
	}
}
