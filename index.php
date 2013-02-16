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




// Initiate Themes and start theme 1
$Theme = new Theme();
var_dump($Theme->getThemes());
echo $Theme->setTheme(1);






// Initiate Arduino KaKu bridge, get some data and switch on light 1
$BridgeArduinoKaKu = new BridgeArduinoKaKu($Config);
var_dump($BridgeArduinoKaKu->getLights());
var_dump($BridgeArduinoKaKu->getLightInfo(1));
$BridgeArduinoKaKu->turnLightOn(1);




//
// Initiate Hue Bridge
//
// If no Hue Bridge configured:
// - find bridge
// - start pairing process
//
//
$BridgeHue = new BridgeHue($Config);


// Turn the light off
var_dump($BridgeHue->setLightState(1, array('on' => false)));
var_dump($BridgeHue->setLightState(2, array('on' => false)));
var_dump($BridgeHue->setLightState(3, array('on' => false)));
sleep(1);

// Xenon-style on
var_dump($BridgeHue->setLightState(1, array('on' => true, 'ct' => 154, 'bri' => 200, 'transitiontime' => 1)));
var_dump($BridgeHue->setLightState(1, array('on' => true, 'ct' => 343, 'bri' => 100, 'transitiontime' => 7)));

// Dump light info
var_dump($BridgeHue->getLightInfo(3));


#todo Use logger
echo "</pre>";