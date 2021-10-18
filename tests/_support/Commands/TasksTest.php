<?php

namespace Tests\Support\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * @internal
 */
final class TasksTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'tasks:test';
    protected $description = 'Tests Tasks';
    protected $usage       = 'tasks:test';

    public function run(array $params = [])
    {
        CLI::write('Commands can output text.');

        $_SESSION['command_tasks_test_did_run'] = true;
    }
}
