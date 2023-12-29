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

namespace CodeIgniter\Tasks;

use CodeIgniter\I18n\Time;
use Exception;
use Throwable;

/**
 * @property ?Throwable $error
 * @property ?string    $output
 * @property Time       $runStart
 * @property Task       $task
 */
class TaskLog
{
    protected Task $task;
    protected ?string $output = null;
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
     * @return string
     *
     * @throws Exception
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
