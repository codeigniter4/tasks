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
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Filters\CITestStreamFilter;
use Tests\Support\TasksTestCase;

/**
 * @internal
 */
final class TaskTest extends TasksTestCase
{
    use DatabaseTestTrait;

    protected $namespace;

    /**
     * @var bool|resource
     */
    protected $streamFilter;

    protected function setUp(): void
    {
        parent::setUp();

        CITestStreamFilter::registration();
        CITestStreamFilter::addOutputFilter();
        CITestStreamFilter::addErrorFilter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        CITestStreamFilter::removeOutputFilter();
        CITestStreamFilter::removeErrorFilter();
    }

    protected function getBuffer(): string
    {
        return CITestStreamFilter::$buffer;
    }

    public function testNamed()
    {
        $task = new Task('command', 'foo:bar');

        // Will build a random name
        $this->assertSame(0, strpos($task->name, 'command_'));

        $task = (new Task('command', 'foo:bar'))->named('foo');

        $this->assertSame('foo', $task->name);
    }

    public function testConstructSavesAction()
    {
        $task = new Task('command', 'foo:bar');

        $result = $this->getPrivateProperty($task, 'action');

        $this->assertSame('foo:bar', $result);
    }

    public function testGetAction()
    {
        $task = new Task('command', 'foo:bar');

        $this->assertSame('foo:bar', $task->getAction());
    }

    public function testGetType()
    {
        $task = new Task('command', 'foo:bar');

        $this->assertSame('command', $task->getType());
    }

    public function testCommandRunsCommand()
    {
        $task = new Task('command', 'tasks:example');

        $task->run();

        $this->assertStringContainsString(
            'Commands can output text.',
            $this->getBuffer(),
        );
    }

    /**
     * `command()` is not buffering the output like it appears it should,
     * so the result is not actually being returned. Disabling this test
     * until the root issue can be resolved.
     */
    //  public function testCommandReturnsOutput()
    //  {
    //      $task   = new Task('command', 'tasks:test');
    //      $result = $task->run();
    //
    //      $this->assertEquals('Commands can output text.', $result);
    //  }

    public function testShouldRunSimple()
    {
        $task = (new Task('command', 'tasks:test'))->hourly();

        $this->assertFalse($task->shouldRun('12:05am'));
        $this->assertTrue($task->shouldRun('12:00am'));
    }

    public function testShouldRunWithEnvironments()
    {
        $originalEnv               = $_SERVER['CI_ENVIRONMENT'];
        $_SERVER['CI_ENVIRONMENT'] = 'development';

        $task = (new Task('command', 'tasks:test'))->environments('development');

        $this->assertTrue($task->shouldRun('12:00am'));

        $_SERVER['CI_ENVIRONMENT'] = 'production';

        $this->assertFalse($task->shouldRun('12:00am'));

        $_SERVER['CI_ENVIRONMENT'] = $originalEnv;
    }

    public function testLastRun()
    {
        helper('setting');
        setting('Tasks.logPerformance', true);

        $task = new Task('closure', static fn () => 1);
        $task->named('foo');

        // Should be dashes when not ran
        $this->assertSame('--', $task->lastRun());

        $date = date('Y-m-d H:i:s');

        // Insert a performance bit in the db
        setting("Tasks.log-{$task->name}", [[
            'task'     => $task->name,
            'type'     => $task->getType(),
            'start'    => $date,
            'duration' => '11.3s',
            'output'   => null,
            'error'    => null,
        ]]);

        // Should return the current time
        $this->assertInstanceOf(Time::class, $task->lastRun()); // @phpstan-ignore-line
        $this->assertSame($date, $task->lastRun()->format('Y-m-d H:i:s'));
    }
}
