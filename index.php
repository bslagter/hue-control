<?php
/**
 * @author Bert Slagter
 */

namespace HueControl;
use \HueControl\Config\Config;
use \HueControl\Client\Client;

//
// Register autoload that loads classes based on namespace
//
require_once( __DIR__ . '/Autoload.php');
spl_autoload_register(array('HueControl\Autoload', 'autoload'));

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

//var_dump($Client->getConfig());
//var_dump($Client->getLights());
//var_dump($Client->getLightInfo(3));

var_dump($Client->setLightState(3, array('on' => true, 'alert' => 'select')));
