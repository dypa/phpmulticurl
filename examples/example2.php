<?php
/* phpMultiCurl
 * @url http://code.google.com/p/phpmulticurl/
 * @license MIT
 */

$urls = array(
	'http://habrahabr.ru/new/',
	'http://habrahabr.ru/new/page2/',
	'http://habrahabr.ru/new/page3/',
	'http://habrahabr.ru/new/page4/',
);

require 'phpQuery.php'; //download latest phpQuery from http://code.google.com/p/phpquery/downloads/list
require '../phpMultiCurl.php';
$curl = new phpMultiCurl;
$curl->setNumThreads(2);

foreach ($urls as $url)
{
	$curl->addUrl(
		$url,
		'onLoad',
		'onError'
	);
}

$curl->load();
unset($curl);

function onLoad($Content, $Info, $Data)
{
	$d = phpQuery::newDocument($Content);
	$r = $d->find('div.hentry');
	foreach ($r as $e)
	{
		$pq = pq($e);
		$title = trim($pq->find('a.topic')->html());
		$num = trim($pq->find('div.comments')->find('span.all')->html());
		if ($num == 'комментировать')
		{
			$num = 0;
		}
		$title = str_replace('<span></span>', '', $title);
		var_dump('['.$title.':'.$num.']');
	}
}
function onError($Error, $Data)
{
	var_dump($Error);
}
