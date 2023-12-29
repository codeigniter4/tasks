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

namespace CodeIgniter\Tasks\Exceptions;

use RuntimeException;

final class TasksException extends RuntimeException
{
    public static function forInvalidTaskType(string $type)
    {
        return new self(lang('Tasks.invalidTaskType', [$type]));
    }

    public static function forInvalidCronExpression(string $string)
    {
        return new self(lang('Tasks.invalidCronExpression', [$string]));
    }
}
