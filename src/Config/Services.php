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

namespace CodeIgniter\Tasks\Config;

use CodeIgniter\Tasks\CronExpression;
use CodeIgniter\Tasks\Scheduler;
use Config\Services as BaseServices;

class Services extends BaseServices
{
    /**
     * Returns the Task Scheduler
     */
    public static function scheduler(bool $getShared = true): Scheduler
    {
        if ($getShared) {
            return static::getSharedInstance('scheduler');
        }

        return new Scheduler();
    }

    /**
     * Returns the CronExpression class.
     */
    public static function cronExpression(bool $getShared = true): CronExpression
    {
        if ($getShared) {
            return static::getSharedInstance('cronExpression');
        }

        return new CronExpression();
    }
}
