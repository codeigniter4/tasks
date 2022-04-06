<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;

return static function (Configuration $config): Configuration {
    return $config
        ->setAdditionalFilesFor('codeigniter4/settings', [
            __DIR__ . '/vendor/codeigniter4/settings/src/Helpers/setting_helper.php',
        ]);
};
