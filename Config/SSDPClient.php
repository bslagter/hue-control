<?php

namespace HueControl\Config;

/**
 * @author Bert Slagter
 */
class SSDPClient
{
	/**
	 * @var array
	 */
	private $ipAddresses;

	/**
	 * @var bool
	 */
	private $debug = true;

	/**
	 *
	 */
	public function __construct()
	{
		$this->ipAddresses = $this->getIpAddresses();
	}

	/**
	 * @return array
	 */
	public function getIpAddresses()
	{
		$ifconfig = `ifconfig -a`;

		preg_match_all('/inet addr:(\d+\.\d+\.\d+\.\d+)/i', $ifconfig, $matches);

		$ipAddresses = array();
		if (!empty($matches[1]) && is_array($matches[1])) {
			foreach ($matches[1] as $ip) {
				if ($ip != '127.0.0.1') {
					$ipAddresses[] = $ip;
				}
			}
		}

		return $ipAddresses;
	}

	/**
	 * Finds the Hue Bridge by doing a SSDO multicast on all interfaces
	 * @return string|bool
	 */
	public function findBridge()
	{
		if (empty($this->ipAddresses) || !is_array($this->ipAddresses)) {
			return false;
		}

		foreach ($this->ipAddresses as $ipAddress) {

			$result = $this->findBridgeForIp($ipAddress);

			if (!empty($result)) {
				return $result;
			}
		}

		return false;
	}

	/**
	 * @param string $ipAddress
	 * @return string|bool
	 */
	private function findBridgeForIp($ipAddress)
	{
		if ($this->debug) {
			echo 'Searching for bridge on ' . $ipAddress . "... \n";
			flush();
		}

		$stream = stream_socket_server('udp://' . $ipAddress . ':1900', $errorNumber, $errorString, STREAM_SERVER_BIND);

		if (!$stream) {
			return false;
		}

		stream_set_blocking($stream, 0);

		$str =  "M-SEARCH * HTTP/1.1\r\n" .
				"HOST: 239.255.255.250:1900\r\n" .
				"MAN: ssdp:discover\r\n" .
				"MX: 10\r\n" .
				"ST: ssdp:all\r\n";

		stream_socket_sendto($stream, $str, 0, '239.255.255.250:1900');

		$timeStart = microtime(true);
		while (true) {

			$pkt = stream_socket_recvfrom($stream, 65536, 0, $peer);

			if (empty($pkt)) {

				usleep(200000);

			} else {

				$bridgeIpAddress = $this->checkIfRemoteIsBridge($pkt);

				if (!empty($bridgeIpAddress)) {
					return $bridgeIpAddress;
				}
			}

			if ((microtime(true) - $timeStart)  > 5) {
				break;
			}
		}
	}

	/**
	 * @param string $packet
	 * @return string|bool
	 */
	private function checkIfRemoteIsBridge($packet)
	{
		if (preg_match('/LOCATION\:\s+(\S+)/i', $packet, $match)) {

			$description = file_get_contents($match[1]);

			if (!empty($description)) {

				$xml = new \SimpleXMLElement($description);

				if (!empty($xml->device->friendlyName)) {

					if (preg_match('/^Philips hue \(([^\)]+)\)$/is', $xml->device->friendlyName, $match)) {

						return $match[1];
					}
				}
			}
		}

		return false;
	}
}
