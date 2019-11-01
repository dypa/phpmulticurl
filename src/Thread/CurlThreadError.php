<?php

namespace PhpMultiCurl\Thread;

final class CurlThreadError
{
    private $errorCode = 0;
    private $errorString = '';

    public function __construct(int $errorCode, string $errorString)
    {
        $this->errorCode = $errorCode;
        $this->errorString = $errorString;
    }

    public function getCode(): int
    {
        return $this->errorCode;
    }

    public function getMessage(): string
    {
        return $this->errorString;
    }

    public function __toString(): string
    {
        return $this->getMessage();
    }
}
