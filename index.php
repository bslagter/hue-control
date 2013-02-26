<?php
/**
 * @author Bert Slagter
 */

namespace HueControl;
use \HueControl\Config\Config;
use \HueControl\Config\Environment;
use \HueControl\Bridge\BridgeHue;
use \HueControl\Bridge\BridgeArduinoKaKu;
use \HueControl\Theme\Theme;

//
// Register autoload that loads classes based on namespace
//
require_once( __DIR__ . '/Autoload.php');
spl_autoload_register(array('HueControl\Autoload', 'autoload'));

include('ui.php');

echo top();
#todo Use logger
echo "<pre>";

//
// Check environment
//
$Environment = new Environment();
$Environment->check();

//
// Load config,
//
$Config = new Config();

// Initiate Themes
$Theme = new Theme();

// Initiate Hue Bridge
$BridgeHue = new BridgeHue($Config);

// Initiate Arduino KaKu bridge
$BridgeArduinoKaKu = new BridgeArduinoKaKu($Config);

echo "<h1>Lights control</h1>";

// If you are switching to a theme, it is handled here
if(isset($_GET['theme'])) {
	if(isset($Theme->getThemes()[$_GET['theme']])) {
		echo "<p>Theme " . $Theme->getThemes()[$_GET['theme']]['name'] . " started</p>";
		$Theme->setTheme($_GET['theme']);
	}
}

// Switching Hue lights
if(isset($_GET['hue']) && isset($_GET['state'])) {
	if($BridgeHue->getLightInfo($_GET['hue']) != null) {
		
		if($_GET['state'] == 1) {
			$BridgeHue->turnLightOn($_GET['hue']);
			echo "<p>Light " . $BridgeHue->getLightInfo($_GET['hue'])['name'] . " swithed on</p>";
		} else {
			$BridgeHue->turnLightOff($_GET['hue']);
			echo "<p>Light " . $BridgeHue->getLightInfo($_GET['hue'])['name'] . " swithed off</p>";
		}
	}
}

// Switching kaku lights
if(isset($_GET['kaku']) && isset($_GET['state'])) {
	if(isset($BridgeArduinoKaKu->getLights()[$_GET['kaku']])) {
		if($_GET['state'] == 1) {
			$BridgeArduinoKaKu->turnLightOn($_GET['kaku']);
			echo "<p>Light " . $BridgeArduinoKaKu->getLights()[$_GET['kaku']]['name'] . " swithed on</p>";
		} else {
			$BridgeArduinoKaKu->turnLightOff($_GET['kaku']);
			echo "<p>Light " . $BridgeArduinoKaKu->getLights()[$_GET['kaku']]['name'] . " swithed off</p>";
		}
	}
}

//List an overview of all available themes
echo "<h2>Themes</h2>";
echo "<ul>";
foreach ($Theme->getThemes() as $key => $theme) {
	echo "<li><a href='index.php?theme=". $key . "'>" . $theme['name'] . "</a></li>";
}
echo "</ul>";


//List an overview of all available lights to switch them on/off
echo "<h2>Lights</h2>";
echo "<h3>Philips Hue lights</h3>";
echo "<ul>";
foreach ($BridgeHue->getLights() as $key => $light) {
	
	if($BridgeHue->getLightInfo($key)['state']['on'] == true) {
		echo "<li><a href='index.php?hue=". $key . "&state=0'>" . $light['name'] . "</a> (On, click to switch off)</li>";
	} else {
		echo "<li><a href='index.php?hue=". $key . "&state=1'>" . $light['name'] . "</a> (Off, click to switch on)</li>";
	}
}
echo "</ul>";
echo "<h3>KaKu lights</h3>";
echo "<ul>";
foreach ($BridgeArduinoKaKu->getLights() as $key => $light) {
	echo "<li>" . $light['name'] . ": <a href='index.php?kaku=". $key . "&state=1'>On</a> / <a href='index.php?kaku=". $key . "&state=0'>Off</a></li>";
}
echo "</ul>";



/*
var_dump($BridgeArduinoKaKu->getLights());
var_dump($BridgeArduinoKaKu->getLightInfo(1));
$BridgeArduinoKaKu->turnLightOn(1);
*/


/*
//
// Initiate Hue Bridge
//
// If no Hue Bridge configured:
// - find bridge
// - start pairing process
//
//
$BridgeHue = new BridgeHue($Config);
*/
/*
// Turn the light off
//var_dump($BridgeHue->setLightState(1, array('on' => false)));
var_dump($BridgeHue->setLightState(2, array('on' => false)));
var_dump($BridgeHue->setLightState(3, array('on' => false)));


sleep(1);

// Xenon-style on
var_dump($BridgeHue->setLightState(1, array('on' => true, 'ct' => 154, 'bri' => 200, 'transitiontime' => 1)));
var_dump($BridgeHue->setLightState(1, array('on' => true, 'ct' => 343, 'bri' => 100, 'transitiontime' => 7)));
*/

/*
// Dump light info
var_dump($BridgeHue->getLightInfo(2));
var_dump($BridgeHue->getLightInfo(3));
*/

#todo Use logger
echo "</pre>";

echo bottom();