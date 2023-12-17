<?php

declare(strict_types=1);

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
            $contents = file_get_contents($file);
            $contents = str_replace('namespace CodeIgniter\\Tasks\\Config', 'namespace Config', $contents);
            $contents = str_replace('use CodeIgniter\\Config\\BaseConfig', 'use CodeIgniter\\Tasks\\Config\\Tasks as BaseTasks', $contents);
            $contents = str_replace('class Tasks extends BaseConfig', 'class Tasks extends BaseTasks', $contents);
            file_put_contents($file, $contents);
        }

        CLI::write(CLI::color('  Published! ', 'green') . 'You can customize the configuration by editing the "app/Config/Tasks.php" file.');
    }
}
