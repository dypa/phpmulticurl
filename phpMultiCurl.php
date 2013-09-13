<?php
/* phpMultiCurl
 * @url https://github.com/dypa/phpmulticurl
 * @license MIT
 */
class phpMultiCurlException extends Exception
{}
class phpMultiCurl
{
	const CURLOPT_IGNORETIMEOUT = 'CURLOPT_IGNORETIMEOUT';

	protected $urls = array();
	protected $threads = array();
	protected $threadsData = array();
	protected $threadsFree = array();

	protected $curlOptions =
		   array(
			CURLOPT_HEADER => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			#417 error with lighttpd
			CURLOPT_HTTPHEADER => array('Expect:'),
			CURLINFO_HEADER_OUT => TRUE,
			#just set
			//self::CURLOPT_IGNORETIMEOUT => TRUE
			);

	protected $mcurl = NULL;
	protected $hash = 0;
	protected $numThreads = 25;

	protected $usleep = 50000;
	protected $select = '0.5';

	public function __construct()
	{
		$this->mcurl = curl_multi_init();
	}

	public function addUrl($Url, $onLoad, $onError, array $Data = array(), array $Options = array())
	{
		$hash = $this->hash();
		if (!is_callable($onLoad) || !is_callable($onError))
		{
			throw new phpMultiCurlException('Bad callback');
		}
		$this->urls[$hash] = array($Url, $onLoad, $onError, $Data, $Options);
		return $hash;
	}

	public function deleteUrl($Hash)
	{
	    if (isset($this->threads[$Hash]))
		{
			curl_close($this->threads[$Hash]);
			unset($this->threads[$Hash]);
		}
		if (isset($this->threadsData[$Hash]))
		{
			unset($this->threadsData[$Hash]);
		}
		unset($this->urls[$Hash]);
	}

	public function load()
	{
		while ($this->countUrls() > 0 || $this->countThreads() > 0)
		{
			$this->checkThreads();
			$running = null;
			do
			{
				do
				{
					usleep($this->usleep);
					$result = curl_multi_exec($this->mcurl, $running);
				}
				while ($result === CURLM_CALL_MULTI_PERFORM);
				$ready = curl_multi_select($this->mcurl, (float) $this->select);
				if($result == CURLM_OK)
				{
					while ($done = curl_multi_info_read($this->mcurl))
					{
						$hash = array_search($done['handle'], $this->threads);
						if ($done['result'] == CURLE_OK)
						{
							$info = curl_getinfo($this->threads[$hash]);
							$content = curl_multi_getcontent($this->threads[$hash]);
							$header=substr($content,0,$info['header_size']);
							$content=substr($content,$info['header_size']);
							$info['response_header'] = $header;
							call_user_func($this->threadsData[$hash]['onLoad'], $content, $info, $this->threadsData[$hash]['data']);
							unset($header);
							unset($info);
							unset($content);
						}
						else
						{
							if ($done['result'] == 28 && isset($this->threadsData[$hash]['options'][self::CURLOPT_IGNORETIMEOUT]))
							{
								$this->threadsData[$hash]['options'][CURLOPT_CONNECTTIMEOUT] = 0;
								$this->threadsData[$hash]['options'][CURLOPT_TIMEOUT] = 2147483647;
								$this->addUrl($this->threadsData[$hash]['url'], $this->threadsData[$hash]['onLoad'], $this->threadsData[$hash]['onError'], $this->threadsData[$hash]['data'], $this->threadsData[$hash]['options']);
							}
							else
							{
								call_user_func($this->threadsData[$hash]['onError'], curl_error($this->threads[$hash]), $this->threadsData[$hash]['data']);
							}
						}
						$this->closeThread($hash);
					}
					$this->checkThreads();
				}
			}
			while ($running > 0  && $ready != -1);
		}
	}

	protected function openThread($Hash, $Url, $onLoad, $onError, $Data, $Options)
	{
		if ($this->countThreadsFree() == 0)
		{
			$curl = curl_init();
		}
		else
		{
			$curl = array_shift($this->threadsFree);
			if (is_resource($curl))
			{
				curl_setopt_array($curl, array());
			}
			else
			{
				$curl = curl_init();
			}
		}
		$Options = $this->array_merge_keys($Options, $this->curlOptions);
		$ops = array();
		foreach ($Options as $k=>$v)
		{
			if (is_int($k))
            {
                $ops[$k] = $v;
            }
		}
		curl_setopt($curl, CURLOPT_URL, $Url);
		curl_setopt_array($curl, $ops);
		$this->threads[$Hash] = $curl;
		$this->threadsData[$Hash]['url'] = $Url;
		$this->threadsData[$Hash]['onLoad'] = $onLoad;
		$this->threadsData[$Hash]['onError'] = $onError;
		$this->threadsData[$Hash]['data'] = $Data;
		$this->threadsData[$Hash]['options'] = $Options;
		curl_multi_add_handle($this->mcurl, $this->threads[$Hash]);
	}

	protected function closeThread($Hash)
	{
		curl_multi_remove_handle($this->mcurl, $this->threads[$Hash]);
		if ($this->numThreads > ($this->countThreadsFree() + $this->countThreads()))
		{
			$this->threadsFree[] = $this->threads[$Hash];
		}
		unset($this->threads[$Hash]);
		unset($this->threadsData[$Hash]);
	}

	protected function checkThreads()
	{
		while ($this->countThreads() < $this->getNumThreads() && $hash = key($this->urls))
		{
			$array = current($this->urls);
			$this->openThread($hash, $array[0], $array[1], $array[2], $array[3], $array[4]);
			unset($this->urls[$hash]);
			reset($this->urls);
		}
	}

	public function __destruct()
	{
		foreach ($this->threads as $k => $thread)
		{
			curl_close($thread);
			unset($this->threads[$k]);
		}
		foreach ($this->threadsFree as $k => $thread)
		{
			curl_close($thread);
			unset($this->threadsFree[$k]);
		}
		curl_multi_close($this->mcurl);
	}

	protected function array_merge_keys($a1, $a2)
	{
		foreach($a2 as $k=>$v)
		{
			if (is_array($v))
			{
				$a1[$k] = $this->array_merge_keys(array_key_exists($k, $a1) ? $a1[$k] : array(), $a2[$k]);
			}
			else
			{
				$a1[$k] = $v;
			}
		}
		return $a1;
	}

	protected function hash()
	{
		$this->hash++;
		return md5($this->hash);
	}

	//HACK for gzip see http://www.php.net/manual/en/function.gzuncompress.php#101643
	public function gzdecode($string)
	{
		return file_get_contents('compress.zlib://data:who/cares;base64,'. base64_encode($string));
	}

	public function setNumThreads($numThreads)
	{
		$this->numThreads = ((int) $numThreads > 0) ? (int) $numThreads : 1;
	}

	public function getNumThreads()
	{
		return $this->numThreads;
	}

	public function countThreads()
	{
		return count($this->threads);
	}

	public function countThreadsFree()
	{
		return count($this->threadsFree);
	}

	public function countUrls()
	{
		return count($this->urls);
	}

	public function setUsleep($int)
	{
		$this->usleep = ((int) $int > 0) ? (int) $int : 1;
	}

	public function getUsleep()
	{
		return $this->usleep;
	}

	public function setSelect($float)
	{
		$this->select = ((float) $float > 0) ? (float) $float : 1;
	}

	public function getSelect()
	{
		return $this->select;
	}
}

