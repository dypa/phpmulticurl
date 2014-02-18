<?php

namespace PhpMultiCurl;

use PhpMultiCurl\Helper\Exception;
use PhpMultiCurl\Helper\Queue;
use PhpMultiCurl\Thread\Manager as ThreadsManager;

class PhpMultiCurl
{
    protected $numberOfThreads = 1;

    public function setNumberOfThreads($number)
    {
        $number = (integer) $number;
        $this->numberOfThreads = $number > 0 ? $number : 1;

        return $this;
    }

    public function getNumberOfThreads()
    {
        return $this->numberOfThreads;
    }

    public function executeTasks(Queue $queue)
    {
        if ($queue->count() === 0) {
            throw new Exception('Task queue can not be empty');
        }

        $manager = new ThreadsManager($this->getNumberOfThreads());
        $manager->executeLoop($queue);

        return true;
    }
}
