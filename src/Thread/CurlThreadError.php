<?php
declare(strict_types=1);

namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Task\BaseTask;

final class CurlThreadError
{
    private $errorCode = 0;
    private $errorString = '';
    private $task;

    public function __construct(int $errorCode, string $errorString, BaseTask $task)
    {
        $this->errorCode = $errorCode;
        $this->errorString = $errorString;
        $this->task = $task;
    }

    public function getCode(): int
    {
        return $this->errorCode;
    }

    public function getMessage(): string
    {
        return $this->errorString;
    }

    public function getTask(): BaseTask
    {
        return $this->task;
    }

    public function __toString(): string
    {
        return $this->getMessage();
    }
}
