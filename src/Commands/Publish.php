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
use CodeIgniter\Publisher\Publisher;
use Throwable;

class Publish extends TaskCommand
{
    protected $name        = 'tasks:publish';
    protected $description = 'Publish Tasks config file into the current application.';

    /**
     * @return void
     */
    public function run(array $params)
    {
        $source = service('autoloader')->getNamespace('CodeIgniter\\Tasks')[0];

        $publisher = new Publisher($source, APPPATH);

        try {
            $publisher->addPaths([
                'Config/Tasks.php',
            ])->merge(false);
        } catch (Throwable $e) {
            $this->showError($e);

            return;
        }

        foreach ($publisher->getPublished() as $file) {
            $publisher->replace(
                $file,
                [
                    'namespace CodeIgniter\\Tasks\\Config' => 'namespace Config',
                    'use CodeIgniter\\Config\\BaseConfig'  => 'use CodeIgniter\\Tasks\\Config\\Tasks as BaseTasks',
                    'class Tasks extends BaseConfig'       => 'class Tasks extends BaseTasks',
                ]
            );
        }

        CLI::write(CLI::color('  Published! ', 'green') . 'You can customize the configuration by editing the "app/Config/Tasks.php" file.');
    }
}
