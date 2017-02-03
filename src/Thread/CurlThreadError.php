<?php

namespace PhpMultiCurl\Thread;

class CurlThreadError
{
    protected $errorCode = 0;
    protected $errorString = '';

    public function __construct($errorCode, $errorString)
    {
        $this->errorCode = $errorCode;
        $this->errorString = $errorString;
    }

    public function getCode()
    {
        return $this->errorCode;
    }

    public function getMessage()
    {
        return $this->errorString;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
