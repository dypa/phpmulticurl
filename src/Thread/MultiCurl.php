<?php
namespace PhpMultiCurl\Thread;

use PhpMultiCurl\Helper\Exception;

class MultiCurl
{
    const SELECT_FAILURE_OR_TIMEOUT = -1;
    const DEFAULT_SELECT_TIMEOUT = 0.05;
    const DEFAULT_EXEC_TIMEOUT = 100;

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
        return \curl_multi_select($this->multiCurlResource, (float) self::DEFAULT_SELECT_TIMEOUT);
    }

    //NOTE CURLM_CALL_MULTI_PERFORM never returns in libcurl >= 7.20.0
    public function execThreads()
    {
        $stillRunning = null;
        do {
            if (\CURLM_OK !== \curl_multi_exec($this->multiCurlResource, $stillRunning)) {
                throw new Exception('curl_multi_exec broken');
            }
            usleep((integer) self::DEFAULT_EXEC_TIMEOUT); //UGLY fix cpu usage
        } while ($stillRunning);

        return true;
    }

    public function readThread()
    {
        return \curl_multi_info_read($this->multiCurlResource);
    }

    public function checkResult(array $result, curlThread $thread)
    {
        return $result['result'] === \CURLE_OK ? null : new CurlThreadError($thread->getErrorCode(), $thread->getErrorMessage());
    }

    public function __destruct()
    {
        \curl_multi_close($this->multiCurlResource);
    }
}
