<?php

namespace PhpMultiCurl\Task;

use PhpMultiCurl\Helper\InvalidArgumentException;
use PhpMultiCurl\Thread\CurlThreadError;

abstract class BaseTask
{
    protected $onLoadCallback = null;
    protected $onErrorCallback = null;
    protected $data = null;
    protected $curlOptions = array();

    //TODO php >= 5.4 callable
    public function setOnLoad($callback)
    {
        if (!\is_callable($callback)) {
            throw new InvalidArgumentException('Not callble');
        }

        $this->onLoadCallback = $callback;

        return $this;
    }

    public function getOnLoad()
    {
        return $this->onLoadCallback;
    }

    public function callOnLoad(array $result)
    {
        \call_user_func($this->getOnLoad(), $result, $this);

        return true;
    }

    //TODO php >= 5.4 callable
    public function setOnError($callback)
    {
        if (!\is_callable($callback)) {
            throw new InvalidArgumentException('Not callble');
        }

        $this->onErrorCallback = $callback;

        return $this;
    }

    public function getOnError()
    {
        return $this->onErrorCallback;
    }

    public function callOnError(CurlThreadError $error)
    {
        \call_user_func($this->getOnError(), $error, $this);

        return true;
    }

    public function callCallbacks($error, array $result)
    {
        if ($error && $this->getOnError()) {
            return $this->callOnError($error);
        } elseif ($error === null && $this->getOnLoad()) {
            return $this->callOnload($result);
        }

        return false;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setCurlOptions(array $options)
    {
        $this->curlOptions = $options;

        return $this;
    }

    public function getCurlOptions()
    {
        return $this->curlOptions;
    }
}
