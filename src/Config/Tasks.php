<?php

namespace CodeIgniter\Tasks\Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Should performance metrics be logged
     * --------------------------------------------------------------------------
     *
     * If true, will log the time it takes for each task to run.
     * Requires the settings table to have been created previously.
     *
     * @var bool
     */
    public $logPerformance = false;

    /**
     * --------------------------------------------------------------------------
     * Maximum performance logs
     * --------------------------------------------------------------------------
     *
     * The maximum number of logs that should be saved per Task.
     * Lower numbers reduced the amount of database required to
     * store the logs.
     *
     * @var int
     */
    public $maxLogsPerTask = 10;

    /**
     * Register any tasks within this method for the application.
     * Called by the TaskRunner.
     */
    public function init(Scheduler $schedule)
    {
        $schedule->command('foo:bar')->daily();

        $schedule->shell('cp foo bar')->daily('11:00 pm');

//         $schedule->call(function() { do something.... })->everyMonday()->named('foo');
    }
}
