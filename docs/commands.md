# CLI Commands

Included in the package are several commands that can be ran from that CLI that provide that bit of emergency
help you might need when something is going wrong with a cron job at 1am on a Saturday. 

All commands are ran through CodeIgniter's `spark` cli tool: 

    > php spark tasks:list
    > php spark tasks:run

## Available Commands

**tasks:list**

    > php spark tasks:list

This will list all available tasks that have been defined in the project, along with their type and
the next time they are scheduled to run.

    +---------------+--------------+-----------------------+---------------------------+
    | Name          | Type         | Next Run              |                           |
    +---------------+--------------+-----------------------+---------------------------+
    | emails        | command      | 2020-03-21-18:30:00   | 1 minute from now         |
    +---------------+--------------+-----------------------+---------------------------+

**tasks:disable**

    > php spark tasks:disable 

Will disable the task runner manually until you enable it again. Writes a file to `{WRITEPATH}/tasks` so 
you need to ensure that directory is writable. Default CodeIgniter permissions already have the WRITEABLE
path with write permissions. You should not need to change anything for this to work. 

**tasks:enable**

    > php spark tasks:enable

Will enable the task runner if it was previously disabled, allowing all tasks to resume running. 

**tasks:run**

    > php spark tasks:run
    
This is the primary entry point to the Tasks system. It should be called by a cron task on the server
every minute in order to be able to effectively run all of the scheduled tasks. You typically will not
run this manually.

    > php spark tasks:run --task foo

To run a specific task even if the specificed scheduled is not met use `--task` argument appended with
the task name.

**tasks:performance**

    > php spark tasks:performance

This command will list a summary of all tasks performance data.

    +---------------+--------------+------------------+------------------+---------+----------+---------------------+---------------------+
    | Name          | Type         | Average Duration | Min/Max Duration | Success | Failures | Last Run            | Last Success        |
    +---------------+--------------+------------------+------------------+---------+----------+---------------------+---------------------+
    | emails        | command      | 0.05 s           | 0.05s / 0.08 s   | 2       | 0        | 2021-05-08 07:38:02 | 2021-05-08 07:38:02 |
    +---------------+--------------+------------------+------------------+---------+----------+---------------------+---------------------+

    > php spark tasks:performance --task foo

To list a detailed summary of a specific task use `--task` argument appended with the task name.

**tasks:performance-table**

    > php spark tasks:performance-table

This command will create the needed migration file for performance tracking.
