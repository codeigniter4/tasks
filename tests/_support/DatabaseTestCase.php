<?php
namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class DatabaseTestCase extends CIUnitTestCase
{
	use DatabaseTestTrait;

	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = true;

	public function setUp(): void
	{
		parent::setUp();
	}

}