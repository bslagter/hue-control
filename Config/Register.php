<?php

namespace HueControl\Config;

/**
 * @author Bert Slagter
 */
class Register
{
	/**
	 * @var string
	 */
	private $ipAddress;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @param string $ipAddress
	 */
	public function __construct($ipAddress)
	{
		$this->ipAddress = $ipAddress;
		$this->key = 'HueControl' . substr(sha1(uniqid()), 0, 24);
	}

	/**
	 * Try to register a new key (need to press button to finish)
	 * @return string|bool
	 */
	public function getNewKey()
	{

		$timeStart = microtime(true);

		while (true) {

			$response = $this->sendRequest();

			if (!empty($response[0]['success']['username'])) {
				return $response[0]['success']['username'];
			}

			if ( (microtime(true) - $timeStart) > 30) {
				return false;
			}

			if (!empty($response[0]['error']['type']) && $response[0]['error']['type'] == 101) {

				echo "Waiting till button is pressed...\n";
				flush();

				// wait till button is pressed...
				sleep(1);
				continue;
			}

			return false;
		}
	}

	/**
	 * @return array
	 */
	private function sendRequest()
	{
		$url = 'http://' . $this->ipAddress . '/api';
		$data = array(
			'username' => $this->key,
			'devicetype' => 'Hue Control API Client',
			);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		return json_decode($response, 1);
	}
}
