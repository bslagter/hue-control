<?php
/**
 * @author Rogier van den Berg
 */

namespace HueControl\Theme;
use \HueControl\Config\Config;
use \HueControl\Bridge\BridgeHue;
use \HueControl\Bridge\BridgeArduinoKaKu;

/**
 * @author Bert Slagter
 */
class Theme
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
	public function __construct()
	{
		$this->Config = new Config();
		$this->checkThemes();
	}

	/**
	 * @param $themeId
	 * @return string
	 * Set specific theme, returns all set light states from individual lamps.
	 * The returnvalue can be used as feedback to display what lamps have been switched into what.
	 */
	public function setTheme($themeId)
	{
		$return = "";

		//echo "\n\nStarting Theme:'" . $this->data[$themeId]['name'] . "'\n";

		foreach($this->data[$themeId]['lightstates'] as $lightstate) {
			if($lightstate['bridge'] == "BridgeHue") {
				$BridgeHue = new BridgeHue($this->Config);
				$BridgeHue->setLightState($lightstate['lightid'], $lightstate['state']);
				$return .= $lightstate['name'] . "\n";

			} elseif ($lightstate['bridge'] == "BridgeArduinoKaKu") {
				$BridgeArduinoKaKu = new BridgeArduinoKaKu($this->Config);
				if($lightstate['state'] == 1) {
					$BridgeArduinoKaKu->turnLightOn($lightstate['lightid']);
				} else {
					$BridgeArduinoKaKu->turnLightOff($lightstate['lightid']);
				}

				$return .= $lightstate['name'] . "\n";

			} else {
				//Other types of bridges are not yet implemented
			}
		}
		//echo $return;
		return $return;
	}

	/**
	 * @return array
	 * Returns an overview of all stored themes.
	 */
	public function getThemes()
	{
		return $this->data;
	}

	/**
	 * Check whether there are Themes in the config file.
	 * If there is no "themes" section in the config, there will be made one as an example, with 4 themes
	 */
	private function checkThemes()
	{
		$this->data = $this->Config->getConfigValue('themes');

		if ($this->data == null) {
			$this->data = array(
    			1 =>  array(
	    			"name" => "Example Theme: Study on, bathroom off",
	    			"lightstates" => array(
	    				array(
	    					"bridge" => "BridgeArduinoKaKu",
	    					"name" => "Lamp Studeerkamer aan",
	    					"lightid" => 1,
	    					"state" => 1
	    					),
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Badkamer uit",
	    					"lightid" => 1,
	    					"state" => array('on' => false),
	    					),
	        		)
	    		),
	    		2 => array(
	    			"name" => "Example Theme 2: Bathroom red",
	    			"lightstates" => array(
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Badkamer aan en rood",
	    					"lightid" => 1,
	    					"state" => array('on' => true, 'bri' => 200, 'hue' => 0, 'sat'=> 254, 'transitiontime' => 7),
	    					),
	        		)
	    		),
	    		3 => array(
	    			"name" => "Example Theme 3: 3 hues off",
	    			"lightstates" => array(
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Badkamer uit",
	    					"lightid" => 1,
	    					"state" => array('on' => false),
	    					),
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Eettafel uit 1/2",
	    					"lightid" => 2,
	    					"state" => array('on' => false),
	    					),
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Eettafel uit 2/2",
	    					"lightid" => 3,
	    					"state" => array('on' => false),
	    					),
	        		)
	    		),
	    		4 => array(
	    			"name" => "Example Theme 3: 3 hues on",
	    			"lightstates" => array(
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Badkamer aan",
	    					"lightid" => 1,
	    					"state" => array('on' => true, 'ct' => 343, 'bri' => 254, 'transitiontime' => 7)
	    					),
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Eettafel aan 1/2",
	    					"lightid" => 2,
	    					"state" => array('on' => true, 'ct' => 343, 'bri' => 254, 'transitiontime' => 7)
	    					),
	    				array(
	    					"bridge" => "BridgeHue",
	    					"name" => "Lamp Eettafel aan 2/2",
	    					"lightid" => 3,
	    					"state" => array('on' => true, 'ct' => 343, 'bri' => 254, 'transitiontime' => 7)
	    					),
	        		)
	    		),

        	);
        	$this->Config->setConfigValue('themes', $this->data);
    	}
	}
}
