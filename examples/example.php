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
	'http://error.loc',
);
$value1 = 'hello';
$value2 = 'world';

require '../phpMultiCurl.php';
$curl = new phpMultiCurl;
//by default 25
$curl->setNumThreads(10);

foreach ($urls as $url)
{
	$curl->addUrl(
		//Url
		$url,
		//events callbacks
		'onLoad',
		'onError',
		//arguments for callbacks (see $Data)
		array('arg1'=>$value1, 'arg2'=>$value2, 'url'=> $url),
		//curl options for url (if need)
		array(
		CURLOPT_HTTPHEADER			=> array(
									"Accept-Language: en-us,en;q=0.5",
									"Accept-Charset: utf-8;q=0.7,*;q=0.7",
									"Cache-Control: max-age=0",
									"Pragma: ",
									"Keep-Alive: 300",
									"Connection: keep-alive",
									),

		CURLOPT_REFERER 			=> "http://www.google.com",
		CURLOPT_USERAGENT 			=> "Googlebot/2.1 (+http://www.google.com/bot.html)",
		CURLOPT_FOLLOWLOCATION		=> TRUE,
		CURLOPT_AUTOREFERER			=> TRUE,
		CURLOPT_MAXREDIRS 			=> 10,
		CURLOPT_CONNECTTIMEOUT		=> 30,
		CURLOPT_TIMEOUT 			=> 30,
		CURLOPT_DNS_CACHE_TIMEOUT	=> 1,
		CURLOPT_SSL_VERIFYHOST		=> FALSE,
		CURLOPT_SSL_VERIFYPEER		=> FALSE,
		)
	);
}

$curl->load();

unset($curl);

function onLoad($Content, $Info, $Data)
{
	if ($Info['http_code'] == 200)
	{
		var_dump('downloaded '.$Data['url']);
		var_dump('content: '.str_replace(array("\r\n", "\n"), '', substr($Content, 0, 512)));
	}
	else
	{
		var_dump('error_page');
	}
	var_dump($Data);
}
function onError($Error, $Data)
{
	var_dump($Error);
	var_dump($Data);
}
