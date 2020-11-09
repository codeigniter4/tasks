<?php

namespace CodeIgniter\Tasks\Exceptions;

use RuntimeException;

class TasksException extends RuntimeException
{
	public static function forInvalidTaskType(string $type)
	{
		return new static(lang('Tasks.invalidTaskType', [$type]));
	}
}
