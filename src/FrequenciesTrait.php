<?php namespace CodeIgniter\Tasks;

/**
 * Trait FrequenciesTrait
 *
 * Provides the methods to assign frequencies to individual tasks.
 *
 * @package CodeIgniter\Tasks
 */

trait FrequenciesTrait
{
	/**
	 * The generated cron expression
	 *
	 * @var array<string|int, string|int>
	 */
	protected $expression = [
		'min'        => '*',
		'hour'       => '*',
		'dayOfMonth' => '*',
		'month'      => '*',
		'dayOfWeek'  => '*',
	];

	/**
	 * If listed, will restrict this to running
	 * within only those environments.
	 *
	 * @var null
	 */
	protected $allowedEnvironments = null;

	/**
	 * Schedules the task through a raw crontab expression string.
	 *
	 * @param string $expression
	 *
	 * @return $this
	 */
	public function cron(string $expression)
	{
		$this->expression = explode(' ', $expression);

		return $this;
	}

	/**
	 * Returns the generated expression.
	 *
	 * @return string
	 */
	public function getExpression()
	{
		return implode(' ', array_values($this->expression));
	}

	/**
	 * Runs daily at midnight, unless a time string is
	 * passed in (like 4:08 pm)
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function daily(string $time = null)
	{
		$min = $hour = 0;

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']  = $min;
		$this->expression['hour'] = $hour;

		return $this;
	}

	/**
	 * Runs at the top of every hour or at a specific minute.
	 *
	 * @return $this
	 */
	public function hourly(int $minute = null)
	{
		$this->expression['min']  = is_null($minute) ? "00" : $minute;
		$this->expression['hour'] = '*';

		return $this;
	}


	/**
	 * Runs at every hour or every x hours
	 *
	 * @param int  $hour
	 * @param null $minute
	 * @return self
	 */
	public function everyHour(int $hour = 1, $minute = null)
	{
		$this->expression['min'] = is_null($minute) ? "0" : $minute;
		$this->expression['hour'] = ($hour == 1) ? "*" : '*/' . $hour;

		return $this;
	}

	/**
	 * Runs in a specific range of hours
	 *
	 * @param int $fromHour
	 * @param int $toHour
	 * @return self
	 */
	public function betweenHours(int $fromHour, int $toHour)
	{
		$this->expression['hour'] = $fromHour . "-" . $toHour;
		return $this;
	}

	/**
	 * Runs on a specific choosen hours
	 *
	 * @param array $hours
	 * @return self
	 */
	public function hours(array $hours = [])
	{
		if (!is_array($hours))
		{
			$hours = [$hours];
		}

		$this->expression['hour'] = implode(",", $hours);

		return $this;
	}

	/**
	 * Set the execution time to every minute or every x minutes.
	 *
	 * @param int|string|null When set, specifies that the job will be run every $minute minutes
	 *
	 * @return self
	 */
	public function everyMinute($minute = null)
	{
		$this->expression['min'] = is_null($minute) ? "*" : '*/' . $minute;

		return $this;
	}

	/**
	 * Runs every 5 minutes
	 *
	 * @return $this
	 */
	public function everyFiveMinutes()
	{
		return $this->everyMinute(5);
	}

	/**
	 * Runs every 15 minutes
	 *
	 * @return $this
	 */
	public function everyFifteenMinutes()
	{
		return $this->everyMinute(15);
	}

	/**
	 * Runs every 30 minutes
	 *
	 * @return $this
	 */
	public function everyThirtyMinutes()
	{
		return $this->everyMinute(30);
	}


	/**
	 * Runs in a specific range of minutes
	 *
	 * @param int $fromMinute
	 * @param int $toMinute
	 * @return self
	 */
	public function betweenMinutes(int $fromMinute,int $toMinute)
	{
		$this->expression['min'] = $fromMinute . "-" . $toMinute;
		return $this;
	}

	/**
	 * Runs on a specific choosen minutes
	 *
	 * @param array $minutes
	 * @return self
	 */
	public function minutes(array $minutes = [])
	{
		if (!is_array($minutes))
		{
			$minutes = [$minutes];
		}

		$this->expression['min'] = implode(",", $minutes);

		return $this;
	}


	/**
	 * Runs on specific days
	 *
	 * @param array|int $days [0 : Sunday - 6 : Saturday]
	 * @return self
	 */
	public function days($days)
	{
		if (!is_array($days))
		{
			$days = [$days];
		}

		$this->expression['dayOfWeek'] = implode(",", $days);

		return $this;
	}

	/**
	 * Runs every Sunday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function sundays(string $time = null)
	{
		return $this->setDayOfWeek(0, $time);
	}

	/**
	 * Runs every monday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function mondays(string $time = null)
	{
		return $this->setDayOfWeek(1, $time);
	}

	/**
	 * Runs every Tuesday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function tuesdays(string $time = null)
	{
		return $this->setDayOfWeek(2, $time);
	}

	/**
	 * Runs every Wednesday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function wednesdays(string $time = null)
	{
		return $this->setDayOfWeek(3, $time);
	}

	/**
	 * Runs every Thursday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function thursdays(string $time = null)
	{
		return $this->setDayOfWeek(4, $time);
	}

	/**
	 * Runs every Friday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function fridays(string $time = null)
	{
		return $this->setDayOfWeek(5, $time);
	}

	/**
	 * Runs every Saturday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function saturdays(string $time = null)
	{
		return $this->setDayOfWeek(6, $time);
	}

	/**
	 * Should run the first day of every month.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function monthly(string $time = null)
	{
		$min = $hour = 0;

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']        = $min;
		$this->expression['hour']       = $hour;
		$this->expression['dayOfMonth'] = 1;

		return $this;
	}

	/**
	 * Runs on specific days of the month
	 *
	 * @param array|int $days [1-31]
	 * @return self
	 */
	public function daysOfMonth($days)
	{
		if (!is_array($days))
		{
			$days = [$days];
		}

		$this->expression['dayOfMonth'] = implode(",", $days);

		return $this;
	}

	/**
	 * Runs on specific months
	 *
	 * @param array $months
	 * @return self
	 */
	public function months(array $months = [])
	{
		$this->expression['month'] = implode(",", $months);
		return $this;
	}

	/**
	 * Should run the first day of each quarter,
	 * i.e. Jan 1, Apr 1, July 1, Oct 1
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function quarterly(string $time = null)
	{
		$min = $hour = 0;

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']        = $min;
		$this->expression['hour']       = $hour;
		$this->expression['dayOfMonth'] = 1;
		$this->expression['month']      = '*/3';

		return $this;
	}

	/**
	 * Should run the first day of the year.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function yearly(string $time = null)
	{
		$min = $hour = 0;

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']        = $min;
		$this->expression['hour']       = $hour;
		$this->expression['dayOfMonth'] = 1;
		$this->expression['month']      = 1;

		return $this;
	}

	/**
	 * Should run M-F.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function weekdays(string $time = null)
	{
		$min = $hour = 0;

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']       = $min;
		$this->expression['hour']      = $hour;
		$this->expression['dayOfWeek'] = '1-5';

		return $this;
	}

	/**
	 * Should run Saturday and Sunday
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function weekends(string $time = null)
	{
		$min = $hour = 0;

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']       = $min;
		$this->expression['hour']      = $hour;
		$this->expression['dayOfWeek'] = '6-7';

		return $this;
	}


	/**
	 * Internal function used by the everyMonday, etc functions.
	 *
	 * @param integer     $day
	 * @param string|null $time
	 *
	 * @return $this
	 */
	protected function setDayOfWeek(int $day, string $time = null)
	{
		$min = $hour = '*';

		if (! empty($time))
		{
			[$min, $hour] = $this->parseTime($time);
		}

		$this->expression['min']       = $min;
		$this->expression['hour']      = $hour;
		$this->expression['dayOfWeek'] = $day;

		return $this;
	}

	/**
	 * Parses a time string (like 4:08 pm) into mins and hours
	 *
	 * @param string $time
	 */
	protected function parseTime(string $time)
	{
		$time = strtotime($time);

		return [
			date('i', $time), // mins
			date('G', $time),
		];
	}
}
