<?php

namespace HueControl\Bridge;
use \HueControl\Config\Config;

/**
 * @author Bert Slagter
 */
class BridgeArduinoKaKu implements Bridge
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

	public function turnLightOn($lightId)
	{
		// send ?cmd=<code>*<ms> to ip of Arduino
	}

	public function turnLightOff($lightId)
	{
		// send ?cmd=<code>*<ms> to ip of Arduino
	}
}
