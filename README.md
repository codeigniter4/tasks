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

## Notes

My "to-do list" for this module:

- [x] provides commands to view when tasks are scheduled to run. Can list all for this week, or on a specific day.
- [x] command to run any job manually
- [x] command to disable/enable a job
- [ ] should have a variety of ways to notify when done, like logging, email, etc
- [x] must be able to call shell commands
- [x] must be able to run closures
- [x] must be able to run commands
- [x] restrict by environment
- [x] can specify the timezone
- [ ] should collect performance information (in writeable as csv)
- [ ] command to view performance (https://github.com/codestudiohq/laravel-totem)
- [ ] provide a debug toolbar pane
