<?php namespace CodeIgniter\Tasks;

class TaskRunner
{
    /**
     * @var Scheduler
     */
    protected $scheduler;

    public function __construct()
    {
        $this->scheduler = service('scheduler');
    }

    public function run()
    {

    }
}
