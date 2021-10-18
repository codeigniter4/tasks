<?php

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\CLI;

/**
 * Disable Task Running.
 */
class Disable extends TaskCommand
{
    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'tasks:disable';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Disables the task runner.';

    /**
     * the Command's usage
     *
     * @var string
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
