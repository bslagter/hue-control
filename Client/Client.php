<?php

namespace HueControl\Client;
use \HueControl\Config\Config;

/**
 * @author Bert Slagter
 */
class Client
{
	/**
	 * @var Config
	 */
	private $Config;

	/**
	 * @param Config $Config
	 */
	public function __construct(Config $Config)
	{
		$this->Config = $Config;
	}

	private function getBaseURL()
	{
		$url = 'http://' .
				$this->Config->getConfigValue('bridgeIp') . '/api/' .
				$this->Config->getConfigValue('bridgeKey');

		return $url;
	}

	private function doGetRequest($path)
	{
		$url = $this->getBaseURL() . '/' . $path;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		$response = json_decode($response, true);

		return $response;
	}

	public function getConfig()
	{
		return $this->doGetRequest('config');
	}

	public function getLights()
	{
		return $this->doGetRequest('lights');
	}

	public function getLightInfo($lightNumber)
	{
		return $this->doGetRequest('lights/' . $lightNumber);
	}


	private function doPutRequest($path, $data)
	{
		$url = $this->getBaseURL() . '/' . $path;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		$response = json_decode($response, true);

		return $response;
	}

	public function setLightState($lightNumber, $params)
	{
		return $this->doPutRequest('lights/' . $lightNumber . '/state', $params);
	}
}
