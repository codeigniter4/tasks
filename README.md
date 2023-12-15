# CodeIgniter Tasks

A task scheduler for CodeIgniter 4.

## Installation

Install via Composer:

    composer require codeigniter4/tasks

Migrate the database:

    php spark migrate --all

## Configuration

Publish the config file:

    php spark tasks:publish

## Defining tasks

Define your tasks in the `init()` method:

```php
// app/Config/Tasks.php
<?php

namespace Config;

use CodeIgniter\Tasks\Config\Tasks as BaseTasks;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseTasks
{
    /**
     * Register any tasks within this method for the application.
     *
     * @param Scheduler $schedule
     */
    public function init(Scheduler $schedule)
    {
        $schedule->command('demo:refresh --all')->mondays('11:00 pm');
    }
}
```

## Docs

Read the full documentation: https://codeigniter4.github.io/tasks/
