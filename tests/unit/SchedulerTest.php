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

use CodeIgniter\Tasks\Scheduler;
use CodeIgniter\Tasks\Task;
use CodeIgniter\Test\CIUnitTestCase as TestCase;

/**
 * @internal
 */
final class SchedulerTest extends TestCase
{
    protected Scheduler $scheduler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scheduler = new Scheduler();
    }

    public function testCallSavesTask()
    {
        $function = static fn () => 'Hello';

        $task = $this->scheduler->call($function);

        $this->assertInstanceOf(Closure::class, $function);
        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($function, $task->getAction());
        $this->assertSame('Hello', $task->getAction()());
    }

    public function testCommandSavesTask()
    {
        $task = $this->scheduler->command('foo:bar');

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame('foo:bar', $task->getAction());
    }

    public function testShellSavesTask()
    {
        $task = $this->scheduler->shell('foo:bar');

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame('foo:bar', $task->getAction());
    }
}
