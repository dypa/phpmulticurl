<?php

namespace PhpMultiCurl\Task;

class Http extends BaseTask
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getCurlOptions()
    {
        $options = parent::getCurlOptions();
        $options[\CURLOPT_URL] = $this->url;
        $options[\CURLOPT_HEADER] = true;
        $options[\CURLOPT_RETURNTRANSFER] = true;
        $options[\CURLINFO_HEADER_OUT] = true;

        return $options;
    }

    public function callOnLoad(array $curlResult)
    {
        $result = \curl_getinfo($curlResult['handle']);
        $content = \curl_multi_getcontent($curlResult['handle']);
        $result['response_header'] = \substr($content, 0, $result['header_size']);
        $result['response_content'] = \substr($content, $result['header_size']);

        parent::callOnLoad($result);
    }
}
