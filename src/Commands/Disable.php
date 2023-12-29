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
