<?php

namespace PhpMultiCurl\Helper;

use SplQueue;
use PhpMultiCurl\Task\BaseTask;

class Queue extends SplQueue
{
    public function enqueue(BaseTask $task)
    {
        return parent::enqueue($task);
    }
}
