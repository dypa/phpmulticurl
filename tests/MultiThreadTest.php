<?php
declare(strict_types=1);

use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\Http as HttpTask;
use PhpMultiCurl\Thread\CurlThreadError;

class MultiThreadTest extends TestCase
{

    public function testFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(
                strlen($response["response_content"]),
                filesize(__DIR__ . "/website/" . basename($task->getUrl()))
            );
        };

        $queue = new TasksQueue();

        $task = (new HttpTask(self::$hostAndPort . '/index.html'))
            ->setOnLoad($onLoad);
        $queue->enqueue($task);

        $task = (new HttpTask(self::$hostAndPort . '/page1.html'))
            ->setOnLoad($onLoad);
        $queue->enqueue($task);

        $task = (new HttpTask(self::$hostAndPort . '/page2.html'))
            ->setOnLoad($onLoad);
        $queue->enqueue($task);

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(3);
        $phpMultiCurl->executeTasks($queue);
    }

    public function testPathNotFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertNotEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        };

        $onError = function (CurlThreadError $error) {
            $this->assertNotEmpty($error->getMessage());
        };

        $queue = new TasksQueue();

        $task = (new HttpTask(self::$hostAndPort . '/abc.html'))
            ->setOnLoad($onLoad)
            ->setOnError($onError);
        $queue->enqueue($task);

        $task = (new HttpTask(self::$hostAndPort . '/def.html'))
            ->setOnLoad($onLoad)
            ->setOnError($onError);
        $queue->enqueue($task);

        $task = (new HttpTask(self::$hostAndPort . '/ghi.html'))
            ->setOnLoad($onLoad)
            ->setOnError($onError);
        $queue->enqueue($task);

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(3);
        $phpMultiCurl->executeTasks($queue);
    }

    public function testHostNotFound(): void
    {
        $onLoad = function (array $response, HttpTask $task) {
            $this->assertEmpty($response["response_content"]);
            $this->assertEquals(404, $response["http_code"]);
        };

        $onError = function (CurlThreadError $error) {
            $this->assertNotEmpty($error->getMessage());
        };

        $queue = new TasksQueue();

        $task = (new HttpTask(self::$hostAndPort . '80/index.html'))
            ->setOnLoad($onLoad)
            ->setOnError($onError);
        $queue->enqueue($task);

        $task = (new HttpTask(self::$hostAndPort . '80/page1.html'))
            ->setOnLoad($onLoad)
            ->setOnError($onError);
        $queue->enqueue($task);

        $task = (new HttpTask(self::$hostAndPort . '80/page2.html'))
            ->setOnLoad($onLoad)
            ->setOnError($onError);
        $queue->enqueue($task);

        $phpMultiCurl = new PhpMultiCurl();
        $phpMultiCurl->setNumberOfThreads(3);
        $phpMultiCurl->executeTasks($queue);
    }

}