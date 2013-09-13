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
$curl->setNumThreads(100);


foreach ($urls as $k=>$url)
{
	$curl->addUrl(
		$url,
		'onLoad',
		'onError',
		array('page'=>$k),
		array(CURLOPT_REFERER => '', CURLOPT_FOLLOWLOCATION => false, CURLOPT_AUTOREFERER => false, CURLOPT_TIMEOUT => 3, CURLOPT_CONNECTTIMEOUT => 3)
	);
}
$curl->load();
unset($curl);

function onLoad($Content, $Info, $Data)
{
	var_dump($Data['page']);
	$header = $Info['response_header'];
	preg_match('/Location:(.*?)\r\n/m', $header, $matches);
	$url = trim(array_pop($matches));
	if ($url == 'http://habrahabr.ru/')
	{
		var_dump('Hello Habr!');
	}
}

function onError($Error, $Data){}
