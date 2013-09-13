<?php
/* phpMultiCurl
 * @url http://code.google.com/p/phpmulticurl/
 * @license MIT
 */

$urls = array(
	'http://google.com',
	'http://ya.ru',
	'http://php.net',
	'http://habr.ru',
);

require '../phpMultiCurl.php';
$curl = new phpMultiCurl;
//by default 25
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

function onLoad($Content, $Info, $Data)
{
	global $curl;
	var_dump('add sub url');
	$curl->addUrl(
		'http://twitter.com',
		'onLoadUrl',
		'onError'
	);
}
function onError($Error, $Data) {}

function onLoadUrl($Content, $Info, $Data)
{
	var_dump('load sub url');
}
