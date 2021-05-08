<@php

namespace {namespace};

use CodeIgniter\Database\Migration;

class {class} extends Migration
{
	public function up()
	{
		$this->forge->addField( [
			'id' => [
				'type'          => 'bigint',
				'constraint'    => 250,
				'auto_increment' => true,
			],
			'name' => [
				'type'          => 'varchar',
				'constraint'    => 250,
			],
			'ran_at' => [
				'type'          => 'datetime',
				'null'          => true,
			],
			'duration' => [
				'type'          => 'float',
				'unsigned'      => true,
			],
			'result' => [
				'type'          => 'text',
			],
			'output' => [
				'type'          => 'longtext',
			],
			'exception' => [
				'type'          => 'longtext',
			],
		]);

		$this->forge->addKey('id', true);

		$this->forge->addKey('name');

		$this->forge->addKey('ran_at');

		$this->forge->createTable('tasks_performance');
	}

	public function down()
	{
		$this->forge->dropTable('tasks_performance');
	}

}
