<?php namespace CodeIgniter\Tasks;

use CodeIgniter\Tasks\Task;

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
	 */
	public function duration()
	{
		$dif = $this->runEnd->difference($this->runStart);

		$minutes = (int)$dif->getMinutes(true);
		$seconds = $dif->getSeconds(true);

		// Since $seconds includes the minutes, calc the extra
		$seconds = $seconds - ($minutes * 60);

		return str_pad((string)$minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);
	}
}
