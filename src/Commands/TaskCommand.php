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

use CodeIgniter\CLI\BaseCommand;

/**
 * Base functionality for enable/disable.
 */
abstract class TaskCommand extends BaseCommand
{
    /**
     * Command grouping.
     */
    protected $group = 'Tasks';

    /**
     * location to save.
     */
    protected string $path = WRITEPATH . 'tasks';
}
