<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Tasks.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Support\Config;

class Registrar
{
    public static function Queue(): array
    {
        return [
            'jobHandlers' => [
                'job-example' => 'Tests\Jobs\Example',
            ],
        ];
    }
}
