# CodeIgniter 4 Tasks

A task scheduler for CodeIgniter 4. 

**NOTE: Just starting development. Not for active consumption or it WILL make your app sick.**

My "to-do list" for this module: 

- provides commands to view when tasks are scheduled to run. Can list all for this week, or on a specific day.
- command to run any job manually
- command to disable/enable a job
- should have a variety of ways to notify when done, like logging, email, etc
- must be able to call shell commands
- must be able to run closures
- must be able to run commands
- restrict by environment
- can specify the timezone
- should collect performance information (in writeable as csv)
- command to view performance (https://github.com/codestudiohq/laravel-totem)
- provide a debug toolbar pane

## How to Try

1. Add the following in your project's `composer.json`:

```
    "require": {
        "codeigniter4/tasks": "dev-develop"
    },
```

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/codeigniter4/tasks.git"
        }
    ],
```

2. Run `composer update`.

3. Run `php spark migrate --all`.

4. Copy `vendor/codeigniter4/settings/src/Config/Tasks.php` into `app/Config/`.

5. Update the namespace in `app/Config/Tasks.php`.

```php
<?php

namespace CodeIgniter\Tasks\Config;
```
↓
```php
<?php

namespace Config;
```
