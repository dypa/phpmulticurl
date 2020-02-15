<?php
declare(strict_types=1);

use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Thread\CurlThreadError;

class MultiThreadTest extends TestCase
{

    public function testFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEquals(preg_replace("#^http://#", "", $response["url"]), $task->getUrl());
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(200, $response["http_code"]);
            $this->assertEquals(
                strlen($response["response_content"]),
                filesize(__DIR__ . "/website/" . basename($task->getUrl()))
            );
        };

        $urls = [
            self::$hostAndPort . '/index.html',
            self::$hostAndPort . '/page1.html',
            self::$hostAndPort . '/page2.html',
        ];

        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad($onLoad);
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }

    public function testPathNotFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEquals(preg_replace("#^http://#", "", $response["url"]), $task->getUrl());
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        };

        $urls = [
            self::$hostAndPort . '/abc.html',
            self::$hostAndPort . '/def.html',
            self::$hostAndPort . '/ghi.html',
        ];

        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad($onLoad);
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }

    public function testHostNotFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEquals(preg_replace("#^http://#", "", $response["url"]), $task->getUrl());
            $this->assertEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        };

        $onError = function (CurlThreadError $error, BaseTask $task) {
            $this->assertEquals("Failed to connect to localhost port 8080: Connection refused", $error->getMessage());
            $this->assertInstanceOf(HttpTask::class, $task);
        };

        $urls = [
            self::$hostAndPort . '80/index.html',
            self::$hostAndPort . '80/page1.html',
            self::$hostAndPort . '80/page2.html',
        ];

        $queue = new TasksQueue();

        foreach ($urls as $url) {
            $task = (new HttpTask($url))
                ->setOnLoad($onLoad)
                ->setOnError($onError);
            $queue->enqueue($task);
        }

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(count($urls));
        $phpMultiCurl->executeTasks($queue);
    }

}