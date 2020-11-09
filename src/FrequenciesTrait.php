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
	 * Runs at the top of every hour.
	 *
	 * @return $this
	 */
	public function hourly()
	{
		$this->expression['min']  = '00';
		$this->expression['hour'] = '*';

		return $this;
	}

	/**
	 * Runs every 5 minutes
	 *
	 * @return $this
	 */
	public function everyFiveMinutes()
	{
		$this->expression['min']  = '/5';
		$this->expression['hour'] = '*';

		return $this;
	}

	/**
	 * Runs every 15 minutes
	 *
	 * @return $this
	 */
	public function everyFifteenMinutes()
	{
		$this->expression['min']  = '/15';
		$this->expression['hour'] = '*';

		return $this;
	}

	/**
	 * Runs every 30 minutes
	 *
	 * @return $this
	 */
	public function everyThirtyMinutes()
	{
		$this->expression['min']  = '/30';
		$this->expression['hour'] = '*';

		return $this;
	}

	/**
	 * Runs every Sunday at midnight, unless time passed in.
	 *
	 * @param string|null $time
	 *
	 * @return $this
	 */
	public function everySunday(string $time = null)
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
	public function everyMonday(string $time = null)
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
	public function everyTuesday(string $time = null)
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
	public function everyWednesday(string $time = null)
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
	public function everyThursday(string $time = null)
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
	public function everyFriday(string $time = null)
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
	public function everySaturday(string $time = null)
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
		$this->expression['month']      = '/3';

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
