<?php

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Tasks\TaskRunner;

/**
 * Enables Task Running
 */
class Enable extends TaskCommand
{
    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'tasks:enable';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Enables the task runner.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'tasks:enable';

    /**
     * Enables task running
     *
     * @param array $params
     */
    public function run(array $params)
    {
        helper('setting');

        setting('Tasks.enabled', true);

        CLI::write('Tasks have been enabled.', 'green');
    }
}
