<?php
declare(strict_types=1);

namespace PhpMultiCurl\Task;

use PhpMultiCurl\Thread\CurlThreadError;

abstract class BaseTask
{
    protected $onLoadCallback = null;
    protected $onErrorCallback = null;
    protected $data = null;
    protected $curlOptions = [];

    public function setOnLoad(callable $callback)
    {
        $this->onLoadCallback = $callback;

        return $this;
    }

    public function getOnLoad(): callable
    {
        return $this->onLoadCallback;
    }

    public function callOnLoad(array $result): bool
    {
        \call_user_func($this->getOnLoad(), $result, $this);

        return true;
    }

    public function setOnError(callable $callback)
    {
        $this->onErrorCallback = $callback;

        return $this;
    }

    public function getOnError(): callable
    {
        return $this->onErrorCallback;
    }

    public function callOnError(CurlThreadError $error): bool
    {
        \call_user_func($this->getOnError(), $error, $this);

        return true;
    }

    public function callCallbacks(?CurlThreadError $error, array $result): bool
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

    public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }

    public function validate(): bool
    {
        return $this->onLoadCallback && $this->onErrorCallback;
    }
}
