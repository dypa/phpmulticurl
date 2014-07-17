<?php

namespace PhpMultiCurl\Helper;

use SplQueue;
use PhpMultiCurl\Task\BaseTask;

class Queue extends SplQueue
{
    public function enqueue($task)
    {
        if (!($task instanceof BaseTask)) {
            throw new Exception('Queue accepts only BaseTask instance');
        }

        return parent::enqueue($task);
    }
}
