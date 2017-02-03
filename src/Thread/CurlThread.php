<?php

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Task\BaseTask;

class CurlThread
{
    protected $curlResource = null;
    protected $task = null;

    public function __construct()
    {
        $this->curlResource = \curl_init();
    }

    public function setTask(BaseTask $task)
    {
        $this->removeTask();
        $this->task = $task;

        return $this;
    }

    /**
     * @return BaseTask
     */
    public function getTask()
    {
        return $this->task;
    }

    public function removeTask()
    {
        $this->task = null;
        $this->resetResourceOptions();

        return $this;
    }

    public function isInUse()
    {
        return $this->task === null ? false : true;
    }

    public function isEqualResource($curlResource)
    {
        return $this->curlResource === $curlResource;
    }

    protected function resetResourceOptions()
    {
        \curl_reset($this->curlResource);

        return $this;
    }

    public function applyCurlOptions()
    {
        \curl_setopt_array($this->curlResource, $this->getTask()->getCurlOptions());

        return $this;
    }

    public function getResource()
    {
        return $this->curlResource;
    }

    public function getErrorMessage()
    {
        return \curl_error($this->getResource());
    }

    public function getErrorCode()
    {
        return \curl_errno($this->getResource());
    }

    public function __destruct()
    {
        \curl_close($this->curlResource);
    }
}
