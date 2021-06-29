<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;

class TasksTestCase extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SESSION = [];
    }
}
