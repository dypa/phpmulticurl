<?php

namespace PhpMultiCurl\Task;

use PhpMultiCurl\Helper\Exception;

abstract class BaseTask
{
    protected $onLoadCallback = null;
    protected $onErrorCallback = null;
    protected $data = null;
    protected $curlOptions = null;

    //TODO php >= 5.4 callable
    public function setOnLoad($callback)
    {
        if (!\is_callable($callback)) {
            throw new Exception('Not callble');
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
            throw new Exception('Not callble');
        }

        $this->onErrorCallback = $callback;

        return $this;
    }

    public function getOnError()
    {
        return $this->onErrorCallback;
    }

    public function callOnError($errorCode, $errorString)
    {
        \call_user_func($this->getOnError(), $errorCode, $errorString, $this);

        return true;
    }

    public function callCallbacks($error, array $result)
    {
        if ($error && $this->getOnError()) {
            $this->callOnError($error[0], $error[1]);
        } elseif ($this->getOnLoad()) {
            $this->callOnload($result);
        }
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
