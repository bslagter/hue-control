<?php

namespace HueControl\Bridge;
use \HueControl\Config\Config;

/**
 * @author Bert Slagter
 */
class BridgeHue implements Bridge
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
		$this->checkConfig();
	}

	public function turnLightOn($lightId)
	{
		$this->setLightState($lightId, array('on' => true));
	}

	public function turnLightOff($lightId)
	{
		$this->setLightState($lightId, array('on' => false));
	}


	/**
	 * @return string
	 */
	private function getBaseURL()
	{
		$url = 'http://' .
				$this->Config->getConfigValue('bridge.hue.ip') . '/api/' .
				$this->Config->getConfigValue('bridge.hue.key');

		return $url;
	}

	/**
	 * @param $path
	 * @return mixed
	 */
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

	/**
	 * @param $path
	 * @param $data
	 * @return mixed
	 */
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

	/**
	 * Check if a Hue Bridge is configured, else start detection procedure
	 */
	private function checkConfig()
	{
		$bridgeIp = $this->Config->getConfigValue('bridge.hue.ip');

		if (empty($bridgeIp)) {
			$BridgeHueFind = new BridgeHueFind();
			$bridgeIp = $BridgeHueFind->findBridge();

			if (!empty($bridgeIp)) {
				$this->Config->setConfigValue('bridge.hue.ip', $bridgeIp);
			} else {
				die ('Could not find bridge');
			}
		}

		$bridgeKey = $this->Config->getConfigValue('bridge.hue.key');

		if (empty($bridgeKey)) {
			$BridgeHueRegister = new BridgeHueRegister($bridgeIp);
			$bridgeKey = $BridgeHueRegister->getNewKey();

			if (!empty($bridgeKey)) {
				$this->Config->setConfigValue('bridge.hue.key', $bridgeKey);
			} else {
				die ('Could not register at the bridge');
			}
		}

		#todo Use logger
		//echo "Bridge found: " . $bridgeIp . "\n";
		//echo "Bridge authenticated: " . $bridgeKey . "\n";
	}
}
