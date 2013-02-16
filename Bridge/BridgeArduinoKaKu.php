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

	public function turnLightOn($lightId)
	{
		$this->broadcastMessage($this->data[$lightId]["on"]);
	}

	public function turnLightOff($lightId)
	{
		$this->broadcastMessage($this->data[$lightId]["off"]);
	}

	public function getLightInfo($lightNumber)
	{
		return $this->data[$lightNumber];
	}

	public function getLights()
	{
		return $this->data;
	}

	private function broadcastMessage($msg)
	{
		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1); 
		$len = strlen($msg);
		socket_sendto($sock, $msg, $len, 0, '255.255.255.255', 8888);
		socket_close($sock);
	}

	private function checkLights()
	{
		
		$this->data = $this->Config->getConfigValue('kakulights');

		if ($this->data == null) {
			$this->data = array(
				1 => array(
	    			"name" => "Studeerkamerlamp",
	    			"on" => "O,2,176978,329,0000",
	    			"off" => "O,2,176982,329,0000",
	    			)
	    		);
        	$this->Config->setConfigValue('kakulights', $this->data);
    	}
	}
}
