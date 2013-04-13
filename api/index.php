<?php
require_once '../Restler/vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Tools');
$r->handle();
