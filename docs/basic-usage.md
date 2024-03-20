# Basic Usage

Tasks are configured with the `app/Config/Tasks.php` config file, inside the `init()` method.
Let's start with a simple example:

```php
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
        $schedule->call(function() {
            DemoContent::refresh();
        })->mondays();
    }
}
```

In this example, we use a closure to refresh demo content at 12:00 am every Monday morning. Closures are
a simple way to handle quick functions like this. You can also execute server commands, execute custom
CLI commands you have written, call a URL, or even fire off an Event of your choosing. Details are covered
below.

## Scheduling

This is how we can schedule our tasks. We have many options.

### Scheduling CLI Commands

If you have written your own [CLI Commands](https://codeigniter.com/user_guide/cli/cli_commands.html), you
can schedule them to run using the `command()` method.

```php
$schedule->command('demo:refresh --all');
```

The only argument is a string that calls the command, complete with an options or arguments.

### Scheduling Shell Commands

You can call out to the server and execute a command using the `shell()` method.

```php
$schedule->shell('cp foo bar')->daily()->at('11:00 pm');
```

Simply provide the command to call and any arguments, and it will be executed using PHP's `exec()` method.

!!! note

    Many shared servers turn off exec access for security reasons. If you will be running
    on a shared server, double-check you can use the `exec` command before using this feature.

### Scheduling Events

If you want to trigger an [Event](https://codeigniter.com/user_guide/extending/events.html) you can
use the `event()` method to do that for you, passing in the name of the event to trigger.

```php
$schedule->event('Foo')->hourly();
```

### Scheduling URL Calls

If you need to ping a URL on a regular basis, you can use the `url()` method to perform a simple
GET request using cURL to the URL you pass in. If you need more dynamism than can be provided in
a simple URL string, you can use a closure or command instead.

```php
$schedule->url('https://my-status-cloud.com?site=foo.com')->everyFiveMinutes();
```

## Frequency Options

There are a number of ways available to specify how often the task is called.


| Method                            | Description                                                           |
|:----------------------------------|:----------------------------------------------------------------------|
| `->cron('* * * * *')`             | Run on a custom cron schedule.                                        |
| `->daily('4:00 am')`              | Runs daily at 12:00am, unless a time string is passed in.             |
| `->hourly() / ->hourly(15)`       | Runs at the top of every hour or at specified minute.                 |
| `->everyHour(3, 15)`              | Runs every 3 hours at XX:15.                                          |
| `->betweenHours(6,12)`            | Runs between hours 6 and 12.                                          |
| `->hours([0,10,16])`              | Runs at hours 0, 10 and 16.                                           |
| `->everyMinute(20)`               | Runs every 20 minutes.                                                |
| `->betweenMinutes(0,30)`          | Runs between minutes 0 and 30.                                        |
| `->minutes([0,20,40])`            | Runs at specific minutes 0,20 and 40.                                 |
| `->everyFiveMinutes()`            | Runs every 5 minutes (12:00, 12:05, 12:10, etc)                       |
| `->everyFifteenMinutes()`         | Runs every 15 minutes (12:00, 12:15, etc)                             |
| `->everyThirtyMinutes()`          | Runs every 30 minutes (12:00, 12:30, etc)                             |
| `->days([0,3])`                   | Runs only on Sunday and Wednesday  ( 0 is Sunday , 6 is Saturday )    |
| `->sundays('3:15am')`             | Runs every Sunday at midnight, unless time passed in.                 |
| `->mondays('3:15am')`             | Runs every Monday at midnight, unless time passed in.                 |
| `->tuesdays('3:15am')`            | Runs every Tuesday at midnight, unless time passed in.                |
| `->wednesdays('3:15am')`          | Runs every Wednesday at midnight, unless time passed in.              |
| `->thursdays('3:15am')`           | Runs every Thursday at midnight, unless time passed in.               |
| `->fridays('3:15am')`             | Runs every Friday at midnight, unless time passed in.                 |
| `->saturdays('3:15am')`           | Runs every Saturday at midnight, unless time passed in.               |
| `->monthly('12:21pm')`            | Runs the first day of every month at 12:00am unless time passed in.   |
| `->daysOfMonth([1,15])`           | Runs only on days 1 and 15.                                           |
| `->everyMonth(4)`                 | Runs every 4 months.                                                   |
| `->betweenMonths(4,7)`            | Runs between months 4 and 7.                                          |
| `->months([1,7])`                 | Runs only on January and July.                                        |
| `->quarterly('5:00am')`           | Runs the first day of each quarter (Jan 1, Apr 1, July 1, Oct 1)      |
| `->yearly('12:34am')`             | Runs the first day of the year.                                       |
| `->weekdays('1:23pm')`            | Runs M-F at 12:00 am unless time passed in.                           |
| `->weekends('2:34am')`            | Runs Saturday and Sunday at 12:00 am unless time passed in.           |
| `->environments('local', 'prod')` | Restricts the task to run only in the specified environments          |


These methods can be combined to create even more nuanced timings:

```php
$schedule->command('foo')
    ->weekdays()
    ->hourly()
    ->environments('development');
```

This would run the task at the top of every hour, Monday - Friday, but only in development environments.

## Naming Tasks

You can name tasks so they can be easily referenced later, such as through the CLI with the `named()` method:

```php
$schedule->command('foo')->nightly()->named('foo-task');
```
