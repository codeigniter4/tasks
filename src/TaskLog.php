<?php

namespace CodeIgniter\Tasks;

use CodeIgniter\I18n\Time;
use Exception;
use Throwable;

/**
 * @property ?Throwable $error
 * @property ?array     $output
 * @property Time       $runStart
 * @property Task       $task
 */
class TaskLog
{
    protected Task $task;
    protected ?array $output = null;
    protected Time $runStart;
    protected Time $runEnd;

    /**
     * The exception thrown during execution, if any.
     */
    protected ?Throwable $error = null;

    /**
     * TaskLog constructor.
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Returns the duration of the task in seconds and fractions of a second.
     *
     * @throws Exception
     *
     * @return string
     */
    public function duration()
    {
        return number_format((float) $this->runEnd->format('U.u') - (float) $this->runStart->format('U.u'), 2);
    }

    /**
     * Magic getter.
     */
    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
    }
}
