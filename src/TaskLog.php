<?php

namespace CodeIgniter\Tasks;

use DateTime;

class TaskLog
{
	/**
	 * @var Task
	 */
	protected $task;

	/**
	 * @var string
	 */
	protected $output;

	/**
	 * @var \CodeIgniter\I18n\Time
	 */
	protected $runStart;

	/**
	 * @var \CodeIgniter\I18n\Time
	 */
	protected $runEnd;

	/**
	 * Status Constants
	 */
	public const STATUS_SUCCESS = "SUCCESS";
	public const STATUS_FAILURE = "FAILURE";


	/**
	 * The exception thrown during execution, if any.
	 *
	 * @var \Throwable
	 */
	protected $error;

	/**
	 * TaskLog constructor.
	 *
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		foreach ($data as $key => $value)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $value;
			}
		}

	}

	/**
	 * Returns the duration of the task in mm:ss format.
	 *
	 * @return string
	 * @throws \Exception
	 * @deprecated
	 */
	public function duration(): string
	{
		$dif = $this->runEnd->difference($this->runStart);

		$minutes = (int)$dif->getMinutes(true);
		$seconds = $dif->getSeconds(true);

		// Since $seconds includes the minutes, calc the extra
		$seconds = $seconds - ($minutes * 60);

		return str_pad((string)$minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);
	}


	/**
	 * Returns the duration of the task in ms
	 *
	 * @return float
	 */
	public function durationInSeconds(): float
	{
		return round(((float) $this->runEnd->format("U.u") - (float) $this->runStart->format("U.u")) , 2);
	}

	/**
	 * Check Weather Perforamnce Loggin Is Enabled Or Not
	 *
	 * @return bool
	 */
	protected function isPerforamnceLoggingEnabled(): bool
	{
		// Don't store performance logs if task doesn't have a name
		if (is_null($this->task->name))
		{
			return false;
		}

		// make sure the performance logging is enabled for the task
		if (!$this->task->performance)
		{
			return false;
		}

		return true;
	}

	/**
	 * Save performance log into the database
	 */
	public function saveToDatabase()
	{
		if( !$this->isPerforamnceLoggingEnabled() ){
			return false;
		}

		$exception = "";

		if ( !empty($this->error) )
		{
			$exception = "[" . $this->error->getFile() . " : " . $this->error->getLine() . "] " . $this->error->getMessage();
		}

		$log = [
			'name' => $this->task->name,
			'ran_at' => date("Y-m-d H:i:s"),
			'duration' => $this->durationInSeconds(),
			'result' => empty($this->error) ? self::STATUS_SUCCESS : self::STATUS_FAILURE,
			'output' => $this->output ?: "",
			'exception' => $exception,
		];

		return db_connect($this->task->connection)
			->table("tasks_performance")
			->insert($log);
	}
}
