<?php

namespace PhpMultiCurl;

use PhpMultiCurl\Helper\Queue;
use PhpMultiCurl\Thread\Manager as ThreadsManager;

class PhpMultiCurl
{
    protected $numberOfThreads = 1;

    public function setNumberOfThreads($number)
    {
        $number = (int) $number;
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
            throw new \LogicException('Task queue can not be empty');
        }

        $manager = new ThreadsManager($this->getNumberOfThreads());
        $manager->executeLoop($queue);

        return true;
    }
}
