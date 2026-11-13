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
use CodeIgniter\Tasks\RunResolver;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

/**
 * @internal
 */
final class RunResolverTest extends TestCase
{
    public function testStopsAfterAllFieldsMatch()
    {
        $next = $this->createMock(Time::class);
        $next->expects($this->once())->method('getMinute')->willReturn('4');
        $next->expects($this->once())->method('getHour')->willReturn('10');
        $next->expects($this->once())->method('getDay')->willReturn('13');
        $next->expects($this->once())->method('getMonth')->willReturn('11');
        $next->expects($this->once())->method('getDayOfWeek')->willReturn('5');

        $this->assertSame($next, (new RunResolver())->nextRun('4 10 13 11 5', $next));
    }

    public function testStopsAfterFindingMatchInLaterIteration()
    {
        $match = $this->createMock(Time::class);
        $match->expects($this->once())->method('getMinute')->willReturn('4');

        $next = $this->createMock(Time::class);
        $next->expects($this->once())->method('getMinute')->willReturn('3');
        $next->expects($this->once())->method('addMinutes')->with(1)->willReturn($match);

        $this->assertSame($match, (new RunResolver())->nextRun('4 * * * *', $next));
    }

    public function testStopsAtIterationLimitWhenNoFieldsMatch()
    {
        $next = $this->createMock(Time::class);
        $next->expects($this->exactly(1000))->method('getMinute')->willReturn('3');
        $next->expects($this->exactly(1000))->method('addMinutes')->with(1)->willReturnSelf();

        $this->assertSame($next, (new RunResolver())->nextRun('4 * * * *', $next));
    }
}
