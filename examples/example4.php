<?php
/* phpMultiCurl
 * @url http://code.google.com/p/phpmulticurl/
 * @license MIT
 */

require '../phpMultiCurl.php';

class _phpMultiCurl extends phpMultiCurl
{
	function hackOpts()
	{
		$this->curlOptions[CURLOPT_USERAGENT] = 'phpMultiCurlBot/1.0 (+http://code.google.com/p/phpmulticurl/)';
		$this->curlOptions[CURLOPT_FOLLOWLOCATION] = TRUE;
		$this->curlOptions[CURLOPT_AUTOREFERER]	= TRUE;
	}
}

$curl = new _phpMultiCurl;
$curl->hackOpts();

$curl->addUrl(
	'http://php.net/',
	'onLoad',
	'onError'
);

$curl->addUrl(
	'http://my.php.net/',
	'onLoad',
	'onError'
);

$curl->load();
unset($curl);

function onLoad($Content, $Info, $Data)
{
	if ($Info['http_code'] == 200)
	{
		var_dump('normal_page');
	}
	else
	{
		var_dump('error_page');
	}
}
function onError($Error, $Data) {}
