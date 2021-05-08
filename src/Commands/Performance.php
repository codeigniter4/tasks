<?php

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Tasks\Task;
use CodeIgniter\Tasks\TaskLog;

/**
 * Shows information on the cache.
 */
class Performance extends TaskCommand
{

	/**
	 * The Command's name
	 *
	 * @var string
	 */
	protected $name = 'tasks:performance';

	/**
	 * the Command's short description
	 *
	 * @var string
	 */
	protected $description = 'Shows a list of all scheduled tasks with their perforamnce data.';

	/**
	 * the Command's usage
	 *
	 * @var string
	 */
	protected $usage = 'tasks:performance';

	/**
	 * The Command's Options
	 *
	 * @var array
	 */
	protected $options = [
		'--task' => 'Get performance data for specific task by alias.',
		'--n' => 'No of performance records to be displayed for a specific task (must be combined with --task).',
	];

	/**
	 * Run The Command
	 *
	 * @param array $params
	 */
	public function run(array $params)
	{

		$scheduler = \Config\Services::scheduler();

		config('Tasks')->init($scheduler);

		$tasks = $scheduler->getTasks();

		$tbody = [];

		// If a task option is passed

		if( $taskName = CLI::getOption("task") ){
			foreach ($tasks as $task)
			{
				if( $task->name !== $taskName ){
					continue;
				}

				$performanceData = $this->getTaskPerforamnceStats($task);

				$noOfRecords = is_numeric( CLI::getOption("n") ) ? (int) CLI::getOption("n") : 10;

				$performanceRecords = $this->getTaskPerformanceRecords($task, $noOfRecords);

				$tbody = [];
				$tbody[] = [
					"Average Execution Duration",
					$performanceData['avg_duration'] ." s"
				];
				$tbody[] = [
					"Minimum Execution Duration",
					$performanceData['min_duration'] ." s"
				];
				$tbody[] = [
					"Maximum Execution Duration",
					$performanceData['max_duration'] ." s"
				];
				$tbody[] = [
					"No of Success Executions",
					$performanceData['no_of_success']
				];
				$tbody[] = [
					"No of Failure Executions",
					$performanceData['no_of_failure']
				];

				/*
				 * Will be added later
				$tbody[] = [
					"Last Run",
					$task->taskLog->getLastRun()
				];
				*/

				$tbody[] = [
					"Next Run",
					$task->nextRun()->format("Y-m-d H:i:s")
				];

				/*
				 * Will be added later
				$tbody[] = [
					"Last Successful Run",
					$task->taskLog->getLastSuccessRun()
				];
				$tbody[] = [
					"Last Failure    Run",
					$task->taskLog->getLastFailedRun()
				];*/

				CLI::write("Task Performance Summary","purple");
				CLI::table($tbody, []);


				CLI::newLine();
				CLI::write("Last $noOfRecords Performance Records : ","purple");

				$tbody = [];
				foreach($performanceRecords as $data){
					$tbody[] = [
						$data['ran_at'],
						($data['result'] == "FAILURE" ? CLI::color("FAILURE","light_red") : CLI::color($data['result'],"cyan")),
						$data['duration'] . " s",
						$data['output'] ?: "-",
						$data['exception'] ?: "-",
					];
				}

				$thead = [
					CLI::color('EXECUTED AT', 'green'),
					CLI::color('RESULT', 'green'),
					CLI::color('DURATION', 'green'),
					CLI::color('OUTPUT', 'green'),
					CLI::color('EXCEPTION', 'green'),
				];

				CLI::table($tbody, $thead);
			}

			return;
		}

		foreach ($tasks as $task)
		{
			// Skip any task which is not named
			if (is_null($task->name))
			{
				continue;
			}

			$performanceData = $this->getTaskPerforamnceStats($task);

			$tbody[] = [
				$task->name,
				ucwords($task->getType()),
				$performanceData['avg_duration'] . "s",
				$performanceData['min_duration'] . "s / " . $performanceData['max_duration'] . "s",
				$performanceData['no_of_success'],
				$performanceData['no_of_failure'],
				//$task->taskLog->getLastRun(),
				//$task->taskLog->getLastSuccessRun(),
			];
		}

		$thead = [
			CLI::color('Name', 'green'),
			CLI::color('Type', 'green'),
			CLI::color('Average Duration', 'green'),
			CLI::color('Min/Max Duration', 'green'),
			CLI::color('Success', 'green'),
			CLI::color('Failures', 'green'),
			//CLI::color('Last Run', 'green'),
			//CLI::color('Last Success', 'green'),
		];

		CLI::table($tbody, $thead);
	}


	/**
	 * @param \CodeIgniter\Tasks\Task $task
	 * @return array
	 */
	protected function getTaskPerforamnceStats(Task $task): array
	{
		$stats = [
			'avg_duration' => '-',
			'min_duration' => '-',
			'max_duration' => '-',
			'no_of_success' => '-',
			'no_of_failure' => '-',
		];

		// Make sure performance stats are enabled for the task
		if (!$task->performance || !$task->name)
		{
			return $stats;
		}

		try
		{
			$array = db_connect($task->connection)
				->table("tasks_performance")
				->select("avg(duration) as durationAvg, min(duration) as durationMin, max(duration) as durationMax, result, count(result) as counter")
				->where('name', $task->name)
				->groupBy("result")
				->get()->getResultArray();
		}
		catch (DatabaseException $e)
		{
			return $stats;
		}

		$avg = 0;
		$min = 0;
		$max = 0;
		$totalRecords = 0;

		foreach ($array as $data)
		{
			$avg += ($data['durationAvg'] * $data['counter']);
			$min += ($data['durationMin'] * $data['counter']);
			$max += ($data['durationMax'] * $data['counter']);
			$totalRecords += $data['counter'];

			if ($data['result'] == TaskLog::STATUS_SUCCESS)
			{
				$stats['no_of_success'] = $data['counter'];
			}
			elseif ($data['result'] == TaskLog::STATUS_FAILURE)
			{
				$stats['no_of_failure'] = $data['counter'];
			}
		}

		if ($totalRecords > 0)
		{
			$stats['avg_duration'] = round($avg / $totalRecords, 3);
			$stats['min_duration'] = round($min / $totalRecords, 3);
			$stats['max_duration'] = round($max / $totalRecords, 3);
		}

		return $stats;
	}

	/**
	 * @param \CodeIgniter\Tasks\Task $task
	 * @param int                     $noOfRecords
	 * @return array
	 */
	protected function getTaskPerformanceRecords(Task $task, int $noOfRecords = 10) : array
	{
		// Make sure performance stats are enabled for the task
		if (!$task->performance || !$task->name)
		{
			return [];
		}

		try
		{
			return db_connect($task->connection)
				->table("tasks_performance")
				->where('name', $task->name)
				->orderBy("ran_at", 'desc')
				->limit($noOfRecords)
				->get()
				->getResultArray();
		}
		catch (DatabaseException $e)
		{
			return [];
		}
	}
}
