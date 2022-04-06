<?php

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
