<?php
/**
 * @author Bert Slagter
 */

#todo: check of CURL + YAML zijn geinstalleerd

namespace HueControl;
use \HueControl\Config\Config;
use \HueControl\Config\Environment;
use \HueControl\Client\Client;

//
// Register autoload that loads classes based on namespace
//
require_once( __DIR__ . '/Autoload.php');
spl_autoload_register(array('HueControl\Autoload', 'autoload'));

echo "<pre>";

//
// Check environment
//
$Environment = new Environment();
$Environment->check();

//
// Load config,
//
// If no config found:
// - create new config file
// - find bridge and start pairing process
//
$Config = new Config();

//
// Initiate Client
//
$Client = new Client($Config);

echo "Bridge found: " . $Config->getConfigValue('bridgeIp') . "\n";
echo "Bridge authenticated: " . $Config->getConfigValue('bridgeKey') . "\n";

//var_dump($Client->getConfig());
//var_dump($Client->getLights());


//var_dump($Client->setLightState(2, array('alert' => 'select1')));
//var_dump($Client->setLightState(2, array('xy' => array(0.8, 0.1))));
//var_dump($Client->setLightState(2, array('xy' => array(0.3, 0.1))));
var_dump($Client->setLightState(2, array('xy' => array(0.5, 0.43))));


var_dump($Client->getLightInfo(2));

echo "</pre>";