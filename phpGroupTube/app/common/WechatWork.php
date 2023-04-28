<?php

namespace app\common;

use Workerman\Worker;

class WechatWork
{
    private static $instance;
    private $task;

    private function __construct()
    {
        $this->task = new Worker();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public  function getTask()
    {
        return $this->task;
    }
}
