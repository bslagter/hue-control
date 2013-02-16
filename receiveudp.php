<?php
/**
 * @author Rogier van den Berg
 */

namespace HueControl;
use \HueControl\Config\Config;
use \HueControl\Theme\Theme;

//
// Register autoload that loads classes based on namespace
//
require_once( __DIR__ . '/Autoload.php');
spl_autoload_register(array('HueControl\Autoload', 'autoload'));


echo "PHP UDP listener started...";
echo "\n\n";

//
// Load config,
//
$Config = new Config();

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_bind($socket, '0.0.0.0', 8889);

$commands = $Config->getConfigValue("commandsarduino");

while (true) {
    $from = '';
    $port = 0;
    socket_recvfrom($socket, $buf, 1024, 0, $from, $port);

    echo "Received: $buf from $from" . PHP_EOL;
    
    /* A command is built with 5 comma separated values:
        * Direction of the message: O or I (outgoing, incoming);
        * Library that applies: 1, 2 or 3 (NewRemoteSwitch, RemoteSwitch or RemoteSensor)
        * Code: xxxx (any value)
        * Period: xxx (microseconds)
        * Additional details: 0000 (4 digits, containing: Type (0=Off, 1=On, 2=Dim), Unit (which light), Dim value (always 0 for now), Group (always 0 for now)
        */
    $message = explode(",", $buf);

    if(!array_key_exists ($message[2], $commands)) { //The command is not known yet, save it as possible command in "commandsarduino" (to start a theme with)

        $commands[$message[2]] = array(
            "name" => "",
            "theme" => 0,
            "library" => $message[1],
            "code" => $message[2],
            "period" => $message[3],
            "details" => $message[4]
            );

        $Config->setConfigValue("commandsarduino", $commands);
        echo "\n\nAdded code $buf to configuration (\"commandsarduino\") file as possible command, you can apply a name and theme to it\n\n";
    } else { 
        if ($commands[$message[2]]['theme'] != 0)
        {
            echo "Starting theme " . $commands[$message[2]]['theme'] . "\n";
            $Theme = new Theme();
            $Theme->setTheme($commands[$message[2]]['theme']);
        }
    }
}
