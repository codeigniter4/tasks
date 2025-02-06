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

namespace Tests\Support\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * @internal
 */
final class TasksExample extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'tasks:example';
    protected $description = 'Tests Example';
    protected $usage       = 'tasks:example';

    public function run(array $params = [])
    {
        CLI::write('Commands can output text.');
    }
}
