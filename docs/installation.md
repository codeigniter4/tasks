# Installation

- [Installation](#installation)
    - [With Composer](#with-composer)
    - [Manual](#manual)
- [Database Migration](#database-migration)

## Installation

### With Composer

The only thing you have to do is to run this command, and you're ready to go.

```console
composer require codeigniter4/tasks
```

### Manual

In the example below we will assume, that files from this project will be located in `app/ThirdParty/tasks` directory.

Download this project and then enable it by editing the `app/Config/Autoload.php` file and adding the `CodeIgniter\Tasks` namespace to the `$psr4` array. You also have to add a companion project [Settings](https://github.com/codeigniter4/settings) in the same fashion, like in the below example:

```php
<?php

// ...

public $psr4 = [
    APP_NAMESPACE => APPPATH, // For custom app namespace
    'Config'      => APPPATH . 'Config',
    'CodeIgniter\Settings' => APPPATH . 'ThirdParty/settings/src',
    'CodeIgniter\Tasks'    => APPPATH . 'ThirdParty/tasks/src',
];

// ...

```

## Database Migration

Regardless of which installation method you chose, we also need to migrate the database to add new tables.

You can do this with the following command:

```console
php spark migrate --all
```

