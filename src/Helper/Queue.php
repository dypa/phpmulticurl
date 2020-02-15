<?php
declare(strict_types=1);

namespace PhpMultiCurl\Helper;

use PhpMultiCurl\Task\BaseTask;
use SplQueue;

class Queue extends SplQueue
{
    public function enqueue($task)
    {
        if (!($task instanceof BaseTask)) {
            throw new \InvalidArgumentException('Queue accepts only BaseTask instance');
        }

        $task->validate();

        parent::enqueue($task);
    }
}
