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

namespace CodeIgniter\Tasks\Test;

use Tests\Support\TasksTestCase;

/**
 * @internal
 */
final class MockTest extends TasksTestCase
{
    protected MockScheduler $scheduler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scheduler = new MockScheduler();
    }

    public function testMockSchedulerCreatesMockTasks()
    {
        $result = $this->scheduler->command('foo:bar');

        $this->assertInstanceOf(MockTask::class, $result);
    }

    public function testMockTaskPreventsRun()
    {
        $task = new MockTask('command', 'tasks:test');
        $task->run();

        $this->assertArrayNotHasKey('command_tasks_test_did_run', $_SESSION);
    }

    public function testMockTaskSetsSession()
    {
        $task = new MockTask('command', 'foo:bar');
        $task->run();

        $this->assertSame(['command', 'foo:bar'], $_SESSION['tasks_cache']);
    }
}
