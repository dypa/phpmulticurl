<?php

namespace PhpMultiCurl\Helper;

use SplQueue;

class Queue extends SplQueue
{
    public function enqueue($task)
    {
        return parent::enqueue($task);
    }
}
