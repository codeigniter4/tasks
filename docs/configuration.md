# Configuration

- [Publishing the Config file](#publishing-the-config-file)
- [Config file options](#config-file-options)
- [Setting the Cron Job](#setting-the-cron-job)

## Publishing the Config file

To make changes to the config file, we have to have our copy in the `app/Config/Tasks.php`. Luckily, this package comes with handy command that will make this easy.

When we run:

    php spark tasks:publish

We will get our copy ready for modifications.

## Config file options

- [$logPerformance](#logperformance)
- [$maxLogsPerTask](#maxlogspertask)


### $logPerformance

Should performance metrics be logged - `bool`.

If `true`, performance information and errors will be logged to the database through the Settings library.
A new record is created each time the task is run.

Default value is `false`.

### $maxLogsPerTask

Maximum performance logs - `int`.

Specifies the maximum number of log files that should be stored for each defined task. Once the maximum is reached
the oldest one is deleted when creating a new one.

Default value is `10`.

## Setting the Cron Job

The last thing to do is to set the Cron Job - you only need to add a single line. Usually you can do this via admin panel provided by your hosting provider.
Remember to replace *path-to-your-project* with an actual path to your project.

```console
* * * * * cd /path-to-your-project && php spark tasks:run >> /dev/null 2>&1
```

This will call your script every minute. When `tasks:run` is called, Tasks will determine the correct tasks that should be run and execute them.
