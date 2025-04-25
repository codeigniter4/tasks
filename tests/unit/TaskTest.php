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
use CodeIgniter\Tasks\Exceptions\TasksException;
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

    public function testSingleInstanceMethod()
    {
        $task = new Task('command', 'foo:bar');

        $this->assertFalse($this->getPrivateProperty($task, 'singleInstance'));

        $result = $task->singleInstance();
        $this->assertTrue($this->getPrivateProperty($task, 'singleInstance'));
        $this->assertNull($this->getPrivateProperty($task, 'singleInstanceTTL'));
        $this->assertSame($task, $result);

        // Test with custom TTL
        $task->singleInstance(3600);
        $this->assertTrue($this->getPrivateProperty($task, 'singleInstance'));
        $this->assertSame(3600, $this->getPrivateProperty($task, 'singleInstanceTTL'));
    }

    public function testGetLockKey()
    {
        $task = new Task('command', 'foo:bar');
        $task->named('test_task');

        $method   = $this->getPrivateMethodInvoker($task, 'getLockKey');
        $expected = 'task_lock_test_task';

        $this->assertSame($expected, $method());

        // Test with unnamed task - should use dynamic name
        $task   = new Task('command', 'foo:bar');
        $method = $this->getPrivateMethodInvoker($task, 'getLockKey');

        // Should use task name from magic getter
        $expected = 'task_lock_' . $task->name;
        $this->assertSame($expected, $method());
    }

    public function testNamedTaskLockConsistency()
    {
        // Create two different closure tasks with the same name
        $closure1 = static fn () => 'test1';

        $closure2 = static function () {
            return 'test2'; // Different functionality
        };

        $task1 = new Task('closure', $closure1);
        $task2 = new Task('closure', $closure2);

        // If they have the same name, they should have the same lock key
        $task1->named('same_name');
        $task2->named('same_name');

        $getLockKey1 = $this->getPrivateMethodInvoker($task1, 'getLockKey');
        $getLockKey2 = $this->getPrivateMethodInvoker($task2, 'getLockKey');

        $this->assertSame($getLockKey1(), $getLockKey2());

        // Different names should produce different keys
        $task3 = new Task('closure', $closure1);
        $task3->named('different_name');

        $getLockKey3 = $this->getPrivateMethodInvoker($task3, 'getLockKey');

        $this->assertNotSame($getLockKey1(), $getLockKey3());
    }

    public function testShouldRunWithSingleInstance()
    {
        $task = (new Task('command', 'foo:bar'))
            ->named('test_should_run')
            ->hourly()
            ->singleInstance();

        // Should run at the right time with no existing lock
        $this->assertTrue($task->shouldRun('12:00am'));

        // Create a lock
        $lockKey = $this->getPrivateMethodInvoker($task, 'getLockKey')();
        cache()->save($lockKey, [], 3600);

        // Should not run if a lock exists
        $this->assertFalse($task->shouldRun('12:00am'));

        cache()->delete($lockKey);
    }

    public function testRunWithSingleInstance()
    {
        $task = new Task('closure', static fn () => 'task executed');
        $task->named('test_run_single');
        $task->singleInstance();

        $result = $task->run();
        $this->assertSame('task executed', $result);

        $lockKey = $this->getPrivateMethodInvoker($task, 'getLockKey')();
        $this->assertNull(cache()->get($lockKey));
    }

    public function testLockReleasedAfterException()
    {
        $task = new Task('command', 'invalid:command');
        $task->named('test_exception');
        $task->singleInstance();

        $reflection = new ReflectionClass($task);
        $property   = $reflection->getProperty('type');
        $property->setValue($task, 'invalid_type');

        $lockKey = $this->getPrivateMethodInvoker($task, 'getLockKey')();

        try {
            $task->run();
            $this->fail('Expected exception was not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(TasksException::class, $e);
        }

        $this->assertNull(cache()->get($lockKey));
    }

    public function testSingleInstanceWithCustomTTL()
    {
        $task = new Task('closure', static fn () => 'done');
        $task->named('test_ttl');

        $task->singleInstance(60);

        $this->assertSame(60, $this->getPrivateProperty($task, 'singleInstanceTTL'));

        $task2 = new Task('closure', static fn () => 'done');
        $task2->named('test_no_ttl');
        $task2->singleInstance();

        $this->assertNull($this->getPrivateProperty($task2, 'singleInstanceTTL'));
    }

    public function testRunQueue()
    {
        $task = new Task('queue', ['example', 'job-example', []]);
        $task->named('test_run_queue');

        $result = $task->run();
        $this->assertTrue($result);

        // No lock
        $lockKey = $this->getPrivateMethodInvoker($task, 'getLockKey')();
        $this->assertNull(cache()->get($lockKey));
    }

    public function testRunQueueWithSingleInstance()
    {
        $task = new Task('queue', ['example', 'job-example', []]);
        $task->named('test_run_queue_single');
        $task->singleInstance();

        $result = $task->run();
        $this->assertTrue($result);

        // Lock is still present
        $lockKey = $this->getPrivateMethodInvoker($task, 'getLockKey')();
        $this->assertNotNull(cache()->get($lockKey));
    }
}
