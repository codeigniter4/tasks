<?php

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Tasks\TaskRunner;

/**
 * Runs current tasks.
 */
class Run extends TaskCommand
{
    /**
     * The Command's name
     */
    protected $name = 'tasks:run';

    /**
     * The Command's Options
     */
    protected $options = [
        '--task' => 'Run specific task by alias.',
    ];

    /**
     * the Command's short description
     */
    protected $description = 'Runs tasks based on the schedule, should be configured as a crontask to run every minute.';

    /**
     * the Command's usage
     */
    protected $usage = 'tasks:run';

    /**
     * Runs tasks at the proper time.
     */
    public function run(array $params)
    {
        helper('setting');

        if (setting('Tasks.enabled') === false) {
            CLI::write(CLI::color('WARNING: Task running is currently disabled.', 'red'));
            CLI::write('To re-enable tasks run: tasks:enable');

            return false;
        }

        CLI::write('Running Tasks...');

        config('Tasks')->init(service('scheduler'));

        $runner = new TaskRunner();

        if (CLI::getOption('task')) {
            $runner->only([CLI::getOption('task')]);
        }

        $runner->run();

        CLI::write(CLI::color('Completed Running Tasks', 'green'));
    }
}
