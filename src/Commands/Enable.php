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
