<?php
declare(strict_types=1);

namespace PhpMultiCurl\Task;

final class Http extends BaseTask
{
    private $url;

    public function __construct(string $url) 
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCurlOptions(): array
    {
        $options = parent::getCurlOptions();
        $options[\CURLOPT_URL] = $this->url;
        $options[\CURLOPT_HEADER] = true;
        $options[\CURLOPT_RETURNTRANSFER] = true;
        $options[\CURLINFO_HEADER_OUT] = true;

        return $options;
    }

    public function callOnLoad(array $curlResult): bool
    {
        $result = \curl_getinfo($curlResult['handle']);
        $content = \curl_multi_getcontent($curlResult['handle']);
        $result['response_header'] = \substr($content, 0, $result['header_size']);
        $result['response_content'] = \substr($content, $result['header_size']);

        return parent::callOnLoad($result);
    }
}
