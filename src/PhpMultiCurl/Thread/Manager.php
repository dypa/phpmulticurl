<?php

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Helper\Exception;
use PhpMultiCurl\Helper\Queue;
use SplObjectStorage;

class Manager
{
    protected $threads = null;
    protected $multiCurl = null;

    public function __construct($numberOfThreads)
    {
        $this->threads = new SplObjectStorage;
        $this->multiCurl = new MultiCurl;

        $this->allocateThreads($numberOfThreads);
    }

    public function find($resource)
    {
        foreach ($this->threads as $thread) {
            if ($thread->isEqualResource($resource)) {
                return $thread;
            }
        }

        throw new Exception ('Resource not found in working threads');
    }

    protected function allocateThreads($numberOfThreads)
    {
        for ($i = 0; $i < $numberOfThreads; $i++) {
            $this->threads->attach(new CurlThread);
        }

        return $this->threads;
    }

    protected function freeThreads()
    {
        foreach ($this->threads as $thread) {
            if ($thread->isInUse()) {
                throw new Exception('Thread in use and can not be closed');
            }

            if (!$this->threads->contains($thread)) {
                throw new Exception("Thread not found in threads");
            }

            $this->multiCurl->removeThread($thread);
            $this->threads->detach($thread);
            unset($thread);
        }

        return $this->threads;
    }

    protected function addThreadToLoop(curlThread $thread, Queue $queue)
    {
        if ($queue->count() > 0 && $task = $queue->dequeue()) {
            $thread->setTask($task);
            $thread->applyCurlOptions();
            $this->multiCurl->addThread($thread);

            return true;
        }

        return false;
    }

    protected function removeThreadFromLoop(curlThread $thread)
    {
        $this->multiCurl->removeThread($thread);
        $thread->removeTask();

        return true;
    }

    public function executeLoop(Queue $queue)
    {
        foreach ($this->threads as $thread) {
            $this->addThreadToLoop($thread, $queue);
        }

        do {
            $this->multiCurl->execThreads();

            $ready = $this->multiCurl->selectThread();
            if ($ready) {
                if ($this->fetchResults($queue)) {
                    $ready = 0; //UGLY thread still in use, because of reuse curl resource
                }
            }
        } while ($ready != -1);

        return true;
    }

    public function fetchResults(Queue $queue)
    {
        $return = false;
        while ($result = $this->multiCurl->readThread()) {
            $thread = $this->find($result['handle']);
            $thread->getTask()->callCallbacks($this->multiCurl->checkResult($result, $thread), $result);

            $this->removeThreadFromLoop($thread);
            $this->addThreadToLoop($thread, $queue);

            $return = true;
        }

        return $return;
    }

    public function __destruct()
    {
        $this->freeThreads();
    }
}
