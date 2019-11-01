<?php

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Helper\Queue;
use SplObjectStorage;

class Manager
{
    private const SELECT_FAILURE_OR_TIMEOUT = -1;
    private const FIX_CPU_USAGE_SLEEP = 250;

    private $threads = null;
    private $multiCurl = null;

    public function __construct(int $numberOfThreads)
    {
        $this->threads = new SplObjectStorage();
        $this->multiCurl = new MultiCurl();

        $this->allocateThreads($numberOfThreads);
    }

    /**
     * @param $resource \resource 
     */
    public function find($resource): CurlThread
    {
        foreach ($this->threads as $thread) {
            if ($thread->isEqualResource($resource)) {
                return $thread;
            }
        }

        throw new \InvalidArgumentException('Resource not found in working threads');
    }

    private function allocateThreads(int $numberOfThreads): SplObjectStorage
    {
        for ($i = 0; $i < $numberOfThreads; ++$i) {
            $this->threads->attach(new CurlThread());
        }

        return $this->threads;
    }

    private function freeThreads(): SplObjectStorage
    {
        foreach ($this->threads as $thread) {
            if ($thread->isInUse()) {
                throw new \RuntimeException('Thread in use and can not be closed');
            }

            if (!$this->threads->contains($thread)) {
                throw new \OutOfBoundsException('Thread not found in threads');
            }

            $this->multiCurl->removeThread($thread);
            $this->threads->detach($thread);
            unset($thread);
        }

        return $this->threads;
    }

    private function addThreadToLoop(curlThread $thread, Queue $queue): bool
    {
        if ($queue->count() > 0 && $task = $queue->dequeue()) {
            $thread->setTask($task);
            $thread->applyCurlOptions();
            $this->multiCurl->addThread($thread);

            return true;
        }

        return false;
    }

    private function removeThreadFromLoop(curlThread $thread): bool
    {
        $this->multiCurl->removeThread($thread);
        $thread->removeTask();

        return true;
    }

    public function executeLoop(Queue $queue): bool
    {
        foreach ($this->threads as $thread) {
            $this->addThreadToLoop($thread, $queue);
        }

        $stillRunning = false;
        do {
            $this->multiCurl->execThreads();
            if (self::SELECT_FAILURE_OR_TIMEOUT === $this->multiCurl->selectThread()) {
                usleep(self::FIX_CPU_USAGE_SLEEP);
            }
            $stillRunning = $this->fetchResults($queue);
        } while ($stillRunning);

        return true;
    }

    public function fetchResults(Queue $queue): bool
    {
        $return = false;
        while ($result = $this->multiCurl->readThread()) {
            $thread = $this->find($result['handle']);
            $thread->getTask()->callCallbacks($this->multiCurl->checkResult($result, $thread), $result);

            $this->removeThreadFromLoop($thread);
            if ($this->addThreadToLoop($thread, $queue)) {
                $return = true;
            }
        }

        return $return;
    }

    public function __destruct()
    {
        $this->freeThreads();
    }
}
