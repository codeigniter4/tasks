<?php

namespace CodeIgniter\Tasks;

use CodeIgniter\Tasks\Task;

class TaskLog
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * @var string
     */
    protected $output;

    /**
     * @var \CodeIgniter\I18n\Time
     */
    protected $runStart;

    /**
     * @var \CodeIgniter\I18n\Time
     */
    protected $runEnd;

    /**
     * The exception thrown during execution, if any.
     *
     * @var \Throwable
     */
    protected $error;

    /**
     * TaskLog constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Returns the duration of the task in seconds and fractions of a second.
     *
     * @return string
     * @throws \Exception
     */
    public function duration()
    {
        return number_format((float)$this->runEnd->format("U.u") - (float)$this->runStart->format("U.u"), 2);
    }

    /**
     * Magic getter.
     *
     * @param string $key
     */
    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
    }
}
