<?php

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Task\BaseTask;

class CurlThread
{
    protected $curlResource;
    protected $task;

    public function __construct()
    {
        $this->curlResource = curl_init();
    }

    public function setTask(BaseTask $task)
    {
        $this->removeTask();
        $this->task = $task;

        return $this;
    }

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

    //TODO resource
    public function isEqualResource( /*resource*/
        $resource)
    {
        return $this->curlResource == $resource ? true : false;
    }

    protected function resetResourceOptions()
    {
        if (PHP_VERSION >= 5.5 && function_exists('curl_reset')) {
            curl_reset($this->curlResource);
        } else {
            curl_close($this->curlResource);
            $this->curlResource = curl_init();
        }

        return $this;
    }

    public function applyCurlOptions()
    {
        curl_setopt_array($this->curlResource, $this->getTask()->getCurlOptions());

        return $this;
    }

    public function getResource()
    {
        return $this->curlResource;
    }

    public function getErrorMessage()
    {
        return curl_error($this->getResource());
    }

    public function getErrorCode()
    {
        return curl_errno($this->getResource());
    }

    public function __destruct()
    {
        curl_close($this->curlResource);
    }
}
