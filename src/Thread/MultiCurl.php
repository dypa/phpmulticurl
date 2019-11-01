<?php

namespace PhpMultiCurl\Thread;

class MultiCurl
{
    private const DEFAULT_SELECT_TIMEOUT = 0.05;

    private $multiCurlResource = null;

    public function __construct()
    {
        $this->multiCurlResource = \curl_multi_init();
    }

    public function addThread(CurlThread $thread): int
    {
        return \curl_multi_add_handle($this->multiCurlResource, $thread->getResource());
    }

    public function removeThread(CurlThread $thread): int
    {
        return \curl_multi_remove_handle($this->multiCurlResource, $thread->getResource());
    }

    public function selectThread(): int
    {
        return \curl_multi_select($this->multiCurlResource, (float) self::DEFAULT_SELECT_TIMEOUT);
    }

    public function execThreads(): void
    {
        $stillRunning = null;
        do {
            if (\CURLM_OK !== \curl_multi_exec($this->multiCurlResource, $stillRunning)) {
                throw new \RuntimeException('curl_multi_exec broken');
            }
        } while ($stillRunning);
    }

    /**
     * @return array|bool
     */
    public function readThread()
    {
        return \curl_multi_info_read($this->multiCurlResource);
    }

    public function checkResult(array $result, curlThread $thread): ?CurlThreadError
    {
        return $result['result'] === \CURLE_OK ? null : new CurlThreadError($thread->getErrorCode(), $thread->getErrorMessage());
    }

    public function __destruct()
    {
        \curl_multi_close($this->multiCurlResource);
    }
}
