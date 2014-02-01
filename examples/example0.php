<?php
require '../vendor/autoload.php';

use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\Http as HttpTask;

if (isset($_GET['sleep'])) {
    sleep($_GET['sleep']);
    echo $_GET['sleep'];
    exit;
}

$onLoad = function ($responce, HttpTask $task) {
    var_dump($responce['response_content']);
};

$onError = function ($errorCode, $errorString, HttpTask $task) {
    var_dump($errorCode);
    var_dump($errorString);
};

$queue = new TasksQueue;

for ($i = 0; $i < 5; $i++) {
    $task = new HttpTask('http://localhost/phpmulticurl/examples/example0.php?sleep=' . (2 * $i));
    $task->setOnLoad($onLoad)->setOnError($onError);
    $queue->enqueue($task);
}
$task = new HttpTask('http://hostname_does_not_exist/');
$queue->enqueue($task->setOnError($onError));


$phpMultiCurl = new PhpMultiCurl();
$phpMultiCurl->setNumberOfThreads(2);
$phpMultiCurl->executeTasks($queue);
