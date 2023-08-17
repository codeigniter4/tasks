<?php

declare(strict_types=1);

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\CLI;

/**
 * Disable Task Running.
 */
class Disable extends TaskCommand
{
    /**
     * The Command's name
     */
    protected $name = 'tasks:disable';

    /**
     * the Command's short description
     */
    protected $description = 'Disables the task runner.';

    /**
     * the Command's usage
     */
    protected $usage = 'tasks:disable';

    /**
     * Disables task running
     */
    public function run(array $params)
    {
        helper('setting');

        setting('Tasks.enabled', false);

        CLI::write('Tasks have been disabled.', 'red');
    }
}
