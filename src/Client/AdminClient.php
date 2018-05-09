<?php

namespace Inkl\PlentyAdmin\Client;

use Curl\Curl;

class AdminClient
{
	const MAX_RECONNECT_TRIES = 10;

	private $reconnectTries = 0;

	/** @var Curl */
	private $curl;
	private $cookieFile;
	private $plentyUrl;
	private $username;
	private $password;

	public function __construct($plentyUrl, $username, $password)
	{
		$this->plentyUrl = $plentyUrl;
		$this->username = $username;
		$this->password = $password;

		$this->curl = new Curl();
		$this->curl->setUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
		$this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);
		$this->curl->setOpt(CURLOPT_COOKIESESSION, true);
		$this->curl->setOpt(CURLOPT_COOKIEJAR, 'cookie');
	}

	public function auth()
	{
		$this->curl->setOpt(CURLOPT_COOKIEFILE, $this->cookieFile);
		$response = $this->curl->post(sprintf('%sapi/ui.php', $this->plentyUrl), [
			'request' => sprintf('{"requests":[{"_dataName":"PlentyMarketsLogin", "_moduleName":"user/login", "_searchParams":{"username":"%s", "password":"%s", "isGWT":true}, "_writeParams":{}, "_validateParams":{}, "_commandStack":[{"type":"read", "command":"read"}], "_dataArray":{}, "_dataList":{}}], "meta":{"token":""}}', $this->username, $this->password)
		]);

		if (!preg_match('/Set-Cookie: SID_PLENTY_ADMIN/is', implode('', $response->response_headers)))
		{
			throw new \Exception('unable to login');
		}
	}

	public function get($call, $params = [])
	{
		$response = $this->curl->get(sprintf('%s%s', $this->plentyUrl, $call), $params);
		if ($response->http_status_code != 200)
		{
			if ($response->http_status_code == 302 && $this->reconnectTries < self::MAX_RECONNECT_TRIES)
			{
				sleep(3);
				$this->reconnectTries++;

				$this->auth();

				return $this->get($call, $params);
			}

			throw new \Exception(sprintf('invalid http code: %d', $response->http_status_code));
		}

		$this->reconnectTries = 0;

		return $response;
	}

	/**
	 * @return string
	 */
	public function getCookieFile()
	{
		if (!$this->cookieFile)
		{
			return tempnam(sys_get_temp_dir(), 'COOKIE');
		}

		return $this->cookieFile;
	}

	/**
	 * @param string $cookieFile
	 * @return $this
	 */
	public function setCookieFile($cookieFile)
	{
		$this->cookieFile = $cookieFile;
		return $this;
	}

}
