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

use CodeIgniter\I18n\Time;
use CodeIgniter\Tasks\Task;
use CodeIgniter\Tasks\TaskLog;
use CodeIgniter\Test\CIUnitTestCase as TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class TaskLogTest extends TestCase
{
    public static function provideDuration(): iterable
    {
        return [
            [
                '2021-01-21 12:00:00',
                '2021-01-21 12:00:00',
                '0.00',
                ['first item', 'second item'],
            ],
            [
                '2021-01-21 12:00:00',
                '2021-01-21 12:00:01',
                '1.00',
                true,
            ],
            [
                '2021-01-21 12:00:00',
                '2021-01-21 12:05:12',
                '312.00',
                null,
            ],
        ];
    }

    /**
     * @param array|bool|int|string|null $output
     *
     * @throws Exception
     */
    #[DataProvider('provideDuration')]
    public function testDuration(string $start, string $end, string $expected, $output)
    {
        $start = new Time($start);
        $end   = new Time($end);

        $log = new TaskLog([
            'task'     => new Task('closure', static function () {}),
            'output'   => $output,
            'runStart' => $start,
            'runEnd'   => $end,
            'error'    => null,
        ]);

        $this->assertSame($expected, $log->duration());
    }
}
