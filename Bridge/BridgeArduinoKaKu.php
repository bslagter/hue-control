<?php

namespace HueControl\Bridge;
use \HueControl\Config\Config;

/**
 * @author Bert Slagter
 * @author Rogier van den Berg
 */
class BridgeArduinoKaKu implements Bridge
{
	/**
	 * @var Config
	 */
	private $Config;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param Config $Config
	 */
	public function __construct(Config $Config)
	{
		$this->Config = $Config;
		$this->checkLights();
	}

	/**
	 * @param $lightId
	 * Turns on light at specific ID
	 */
	public function turnLightOn($lightId)
	{
		$this->broadcastMessage($this->data[$lightId]["on"]);
	}

	/**
	 * @param $lightId
	 * Turns off light at specific ID
	 */
	public function turnLightOff($lightId)
	{
		$this->broadcastMessage($this->data[$lightId]["off"]);
	}

	/**
	 * @param $lightID
	 * @return array
	 * Returns all details that are stored for a specific light
	 */
	public function getLightInfo($lightId)
	{
		return $this->data[$lightId];
	}

	/**
	 * @return array
	 * Returns an overview of all lights that are stored.
	 */
	public function getLights()
	{
		return $this->data;
	}

	/**
	 * @param $msg
	 * Sends out command to Arduino, over UDP broadcast, on port 8888
	 */
	private function broadcastMessage($msg)
	{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1); 
		$len = strlen($msg);
		socket_sendto($sock, $msg, $len, 0, '255.255.255.255', 8888);
		socket_close($sock);
	}

	/**
	 * Checks if the Arduino Bridge can be properly configured and has known lights and commands.
	 * If there is no "kakulights" section in the config, there will be made one as an example
	 */
	private function checkLights()
	{
		
		$this->data = $this->Config->getConfigValue('kakulights');

		if ($this->data == null) {
			$this->data = array(
				1 => array(
	    			"name" => "Dummy KaKu light",
	    			"on" => "O,2,176978,329,0000",
	    			"off" => "O,2,176982,329,0000",
	    			)
	    		);
        	$this->Config->setConfigValue('kakulights', $this->data);
    	}
	}
}
