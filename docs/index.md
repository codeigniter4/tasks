# CodeIgniter Task Scheduler

This makes scheduling Cron Jobs in your application simple, flexible, and powerful. Instead of setting up
multiple Cron Jobs on each server your application runs on, you only need to setup a single cronjob to
point to the script, and then all of your tasks are scheduled in your code.

Besides that, it provides CLI tools to help you manage the tasks that should be ran, and more.

This library relies on [CodeIgniter\Settings](https://github.com/codeigniter4/settings) library to store
information, which provides a convenient way of storing settings in the database or a config file.

### Quickstart

Install via Composer:

```console
composer require codeigniter4/tasks
```

And schedule the task:

```php
<?php
// ...
class Tasks extends BaseTasks
{
    // ...
    public function init(Scheduler $schedule)
    {
        $schedule->command('foo')->weekdays()->hourly();
    }
}
```
