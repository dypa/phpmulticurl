<?php
require '../vendor/autoload.php';
use PhpMultiCurl\Helper\Queue as TasksQueue;
use PhpMultiCurl\PhpMultiCurl;
use PhpMultiCurl\Task\BaseTask;
use PhpMultiCurl\Task\Http as HttpTask;

$urls = [
    'http://google.com',
    'http://ya.ru',
    'http://php.net',
    'http://habr.ru',
    'http://error.loc',
];
$value1 = 'foo';
$value2 = 'bar';
$onLoad = function ($responce, BaseTask $task) {
    var_dump(date('H:i:s'));
    $data = $task->getData();
    if ($responce['http_code'] == 200) {
        var_dump('downloaded ' . $data['url']);
        var_dump('content: ' . str_replace(["\r\n", "\n"], '', substr($responce['response_content'], 0, 512)));
    } else {
        var_dump('error_page');
    }
    flush();
};
$onError = function ($errorCode, $errorString, BaseTask $task) {
    var_dump(date('H:i:s'));
    var_dump($errorCode);
    var_dump($errorString);
    flush();
};


$queue = new TasksQueue;
foreach ($urls as $url) {
    $task = new HttpTask($url);
    $task->setOnLoad($onLoad)->setOnError($onError)->setData(['arg1' => $value1, 'arg2' => $value2, 'url' => $url]);

    $task->setCurlOptions([
        CURLOPT_HTTPHEADER => [
            "Accept-Language: en-us,en;q=0.5",
            "Accept-Charset: utf-8;q=0.7,*;q=0.7",
            "Cache-Control: max-age=0",
            "Pragma: ",
            "Keep-Alive: 300",
            "Connection: keep-alive",
        ],

        CURLOPT_REFERER => "http://www.google.com",
        CURLOPT_USERAGENT => "Googlebot/2.1 (+http://www.google.com/bot.html)",
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_AUTOREFERER => TRUE,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_DNS_CACHE_TIMEOUT => 1,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
    ]);

    $queue->enqueue($task);
}

$phpMultiCurl = new PhpMultiCurl();
$phpMultiCurl->executeTasks($queue);
