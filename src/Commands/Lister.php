<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Tasks.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

/**
 * Lists currently scheduled tasks.
 */
class Lister extends TaskCommand
{
    /**
     * The Command's name
     */
    protected $name = 'tasks:list';

    /**
     * the Command's short description
     */
    protected $description = 'Lists the tasks currently set to run.';

    /**
     * the Command's usage
     */
    protected $usage = 'tasks:list';

    /**
     * Lists upcoming tasks
     */
    public function run(array $params)
    {
        helper('setting');

        if (setting('Tasks.enabled') === false) {
            CLI::write('WARNING: Task running is currently disabled.', 'red');
            CLI::write('To re-enable tasks run: tasks:enable');
            CLI::newLine();
        }

        $scheduler = service('scheduler');

        config('Tasks')->init($scheduler);

        $tasks = [];

        foreach ($scheduler->getTasks() as $task) {
            $cron = service('cronExpression');

            $nextRun = $cron->nextRun($task->getExpression());
            $lastRun = $task->lastRun();

            $tasks[] = [
                'name'     => $task->name ?: $task->getAction(),
                'type'     => $task->getType(),
                'schedule' => $task->getExpression(),
                'last_run' => $lastRun instanceof Time ? $lastRun->toDateTimeString() : $lastRun,
                'next_run' => $nextRun,
                'runs_in'  => $nextRun->humanize(),
            ];
        }

        usort($tasks, static fn ($a, $b) => ($a['next_run'] < $b['next_run']) ? -1 : 1);

        CLI::table($tasks, [
            'Name',
            'Type',
            'Schedule',
            'Last Run',
            'Next Run',
            'Runs',
        ]);
    }
}
