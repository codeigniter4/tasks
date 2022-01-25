<?php

namespace CodeIgniter\Tasks;

use CodeIgniter\I18n\Time;
use Exception;
use Throwable;

class TaskLog
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * @var string|null
     */
    protected $output;

    /**
     * @var Time
     */
    protected $runStart;

    /**
     * @var Time
     */
    protected $runEnd;

    /**
     * The exception thrown during execution, if any.
     *
     * @var Throwable|null
     */
    protected $error;

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
