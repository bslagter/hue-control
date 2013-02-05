<?php

namespace HueControl\Bridge;
use \HueControl\Config\Config;

/**
 * @author Bert Slagter
 */
interface Bridge
{
	/**
	 * Initializes the bridge based on configuration
	 * @param Config $Config
	 */
	public function __construct(Config $Config);

	/**
	 * Turn on the light
	 * @param string $lightId
	 */
	public function turnLightOn($lightId);

	/**
	 * Turn off the light
	 * @param string $lightId
	 */
	public function turnLightOff($lightId);
}