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

class RunResolver
{
    /**
     * The maximum number of times to loop
     * when looking for next run date.
     */
    protected int $maxIterations = 1000;

    /**
     * Takes a cron expression, i.e. '* * * * 4', and returns
     * a Time instance that represents that next time that
     * expression would run.
     */
    public function nextRun(string $expression, Time $next): Time
    {
        // Break the expression into separate parts
        [
            $minute,
            $hour,
            $monthDay,
            $month,
            $weekDay,
        ] = explode(' ', $expression);

        $cron = [
            'minute'   => $minute,
            'hour'     => $hour,
            'monthDay' => $monthDay,
            'month'    => $month,
            'weekDay'  => $weekDay,
        ];

        // We don't need to satisfy '*' values, so
        // remove them to have less to loop over.
        $cron = array_filter($cron, static fn ($item) => $item !== '*');

        // If there's nothing left then it's every minute
        // so set it to one minute from now.
        if ($cron === []) {
            return $next->addMinutes(1)->setSecond(0);
        }

        // Loop over each of the remaining $cron elements
        // until we manage to satisfy all of the them
        for ($i = 1; $i <= $this->maxIterations; $i++) {
            foreach ($cron as $position => $value) {
                $satisfied = false;

                // The method to use on the Time instance
                $method = 'get' . ucfirst($position);

                // monthDay and weekDay need custom methods
                if ($position === 'monthDay') {
                    $method = 'getDay';
                }
                if ($position === 'weekDay') {
                    $method = 'getDayOfWeek';

                    $value = $this->convertDOWToNumbers($value);
                }
                $nextValue = $next->{$method}();

                // If it's a single value
                if ($nextValue === $value) {
                    $satisfied = true;
                }
                // If the value is a list
                elseif (strpos($value, ',') !== false) {
                    if ($this->isInList($nextValue, $value)) {
                        $satisfied = true;
                    }
                }
                // If the value is a range
                elseif (strpos($value, '-') !== false) {
                    if ($this->isInRange($nextValue, $value)) {
                        $satisfied = true;
                    }
                }
                // If the value is an increment
                elseif (strpos($value, '/') !== false) {
                    if ($this->isInIncrement($nextValue, $value)) {
                        $satisfied = true;
                    }
                }

                // If we didn't match it, then start the iterations over
                if (! $satisfied) {
                    $next = $this->increment($next, $position);

                    continue 2;
                }
            }
        }

        return $next;
    }

    /**
     * Increments the part of the cron to the next appropriate.
     *
     * Note: this is a pretty brute-force way to do it. We could
     * definitely make it smarter in the future to cut down on the
     * amount of iterations needed.
     */
    protected function increment(Time $next, string $position): Time
    {
        switch ($position) {
            case 'minute':
                $next = $next->addMinutes(1);
                break;

            case 'hour':
                $next = $next->addHours(1);
                break;

            case 'monthDay':
            case 'weekDay':
                $next = $next->addDays(1);
                break;

            case 'month':
                $next = $next->addMonths(1);
                break;
        }

        return $next;
    }

    /**
     * Determines if the given value is in the specified range.
     *
     * @param int|string $value
     */
    protected function isInRange($value, string $range): bool
    {
        [$start, $end] = explode('-', $range);

        return $value >= $start && $value <= $end;
    }

    /**
     * Determines if the given value is in the specified list of values.
     *
     * @param int|string $value
     */
    protected function isInList($value, string $list): bool
    {
        $list = explode(',', $list);

        return in_array(trim($value), $list, true);
    }

    /**
     * Determines if the $value is one of the increments.
     *
     * @param int|string $value
     */
    protected function isInIncrement($value, string $increment): bool
    {
        [$start, $increment] = explode('/', $increment);

        // Allow for empty start values
        if ($start === '' || $start === '*') {
            $start = 0;
        }

        // The $start interval should be the first one to test against
        if ($value === $start) {
            return true;
        }

        return ($value - $start) > 0
               && (($value - $start) % $increment) === 0;
    }

    /**
     * Given a cron setting for Day of Week, will convert
     * settings with text days of week (Mon, Tue, etc)
     * into numeric values for easier handling.
     */
    protected function convertDOWToNumbers(string $origValue): string
    {
        $origValue = strtolower(trim($origValue));

        // If it doesn't contain any letters, just return it.
        preg_match('/\w/', $origValue, $matches);

        if ($matches === []) {
            return $origValue;
        }

        $days = [
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        return str_replace(array_keys($days), array_values($days), $origValue);
    }
}
