<?php
namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Helper\Exception;

class MultiCurl
{
    const multiCurlSelectTimeout = 0.05;
    const multiCurlExecTimeout = 100;

    protected $multiCurlResource = null;

    public function __construct()
    {
        $this->multiCurlResource = \curl_multi_init();
    }

    public function addThread(CurlThread $thread)
    {
        return \curl_multi_add_handle($this->multiCurlResource, $thread->getResource());
    }

    public function removeThread(CurlThread $thread)
    {
        return \curl_multi_remove_handle($this->multiCurlResource, $thread->getResource());
    }

    public function selectThread()
    {
        return \curl_multi_select($this->multiCurlResource, (float)self::multiCurlSelectTimeout);
    }

    //NOTE CURLM_CALL_MULTI_PERFORM never returns in libcurl >= 7.20.0
    public function execThreads()
    {
        $stillRunning = null;
        do {
            if (\CURLM_OK !== \curl_multi_exec($this->multiCurlResource, $stillRunning)) {
                throw new Exception('curl_multi_exec broken');
            }
            usleep((int)self::multiCurlExecTimeout); //UGLY fix to many loop
        } while ($stillRunning);

        return true;
    }

    public function readThread()
    {
        return \curl_multi_info_read($this->multiCurlResource);
    }

    public function checkResult(array $result, curlThread $thread)
    {
        return $result['result'] === \CURLE_OK ? null : [$thread->getErrorCode(), $thread->getErrorMessage()];
    }

    public function __destruct()
    {
        \curl_multi_close($this->multiCurlResource);
    }
}
