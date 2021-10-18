<?php

namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;

/**
 * Base functionality for enable/disable.
 */
abstract class TaskCommand extends BaseCommand
{
    /**
     * Command grouping.
     *
     * @var string
     */
    protected $group = 'Tasks';

    /**
     * location to save.
     */
    protected $path = WRITEPATH . 'tasks';
}
