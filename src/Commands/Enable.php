<?php

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\CLI;

/**
 * Enables Task Running
 */
class Enable extends TaskCommand
{
    /**
     * The Command's name
     */
    protected $name = 'tasks:enable';

    /**
     * the Command's short description
     */
    protected $description = 'Enables the task runner.';

    /**
     * the Command's usage
     */
    protected $usage = 'tasks:enable';

    /**
     * Enables task running
     */
    public function run(array $params)
    {
        helper('setting');

        setting('Tasks.enabled', true);

        CLI::write('Tasks have been enabled.', 'green');
    }
}
