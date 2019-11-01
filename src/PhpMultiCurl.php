<?php
declare(strict_types=1);

namespace PhpMultiCurl;

use PhpMultiCurl\Helper\Queue;
use PhpMultiCurl\Thread\Manager as ThreadsManager;

final class PhpMultiCurl
{
    private $numberOfThreads = 1;

    public function setNumberOfThreads(int $number): void
    {
        $this->numberOfThreads = $number > 0 ? $number : 1;
    }

    public function getNumberOfThreads(): int
    {
        return $this->numberOfThreads;
    }

    public function executeTasks(Queue $queue): bool
    {
        if ($queue->count() === 0) {
            throw new \LogicException('Task queue can not be empty');
        }

        $manager = new ThreadsManager($this->getNumberOfThreads());
        $manager->executeLoop($queue);

        return true;
    }
}
