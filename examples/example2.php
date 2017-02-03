<?php

require '../vendor/autoload.php';
use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\Http as HttpTask;

$queue = new TasksQueue();

$callback = function (array $response, HttpTask $task) {
    var_dump('parent '.$response['http_code'].' '.$task->getUrl());
    global $queue;
    $task = new HttpTask('http://github.com');
    $task->setOnLoad(function ($response, HttpTask $task) {
        var_dump('child '.$response['http_code'].' '.$task->getUrl());
    });
    $queue->enqueue($task);
};
$task = new HttpTask('http://php.net');
$task->setOnLoad($callback);
$queue->enqueue($task);

$phpMultiCurl = new PhpMultiCurl();
$phpMultiCurl->executeTasks($queue);
