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

namespace Tests\Support\Jobs;

use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;

class Example extends BaseJob implements JobInterface
{
    public function process(): bool
    {
        return true;
    }
}
