# CodeIgniter Tasks

A task scheduler for CodeIgniter 4.

[![PHPUnit](https://github.com/codeigniter4/tasks/actions/workflows/phpunit.yml/badge.svg)](https://github.com/codeigniter4/tasks/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/codeigniter4/tasks/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codeigniter4/tasks/actions/workflows/phpstan.yml)
[![Deptrac](https://github.com/codeigniter4/tasks/actions/workflows/deptrac.yml/badge.svg)](https://github.com/codeigniter4/tasks/actions/workflows/deptrac.yml)
[![Coverage Status](https://coveralls.io/repos/github/codeigniter4/tasks/badge.svg?branch=develop)](https://coveralls.io/github/codeigniter4/tasks?branch=develop)

![PHP](https://img.shields.io/badge/PHP-%5E7.4-blue)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%5E4.1-blue)

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
