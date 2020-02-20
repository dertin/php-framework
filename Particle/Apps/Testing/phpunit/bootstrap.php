<?php

// Bootstrap for PHPUnit

// Constants configuration
require_once '../../../../config.php';
configFramework();

// Autoload
require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload.php';

// init Session
$session = Particle\Core\Session::singleton();
$_SERVER['HTTP_HOST'] = HTTP_HOST;
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'phpunit';
$_SERVER['HTTPS'] = 1;
