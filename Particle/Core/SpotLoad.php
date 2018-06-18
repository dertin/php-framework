<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @name SpotLoad
 *
 *  @category Particle\Core
 *
 *  @author dertin
 *  @abstract
 **/

class SpotLoad
{
    protected static $spotInstance = null;

    public function __construct()
    {
        if (!isset(self::$spotInstance) || !(self::$spotInstance instanceof \Spot\Locator)) {
            $cfg = new \Spot\Config();

            $cfg->addConnection(DB_TYPE_CONFIG, [
              'dbname' => DB_NAME_CONFIG,
              'user' => DB_USER_CONFIG,
              'password' => DB_PASS_CONFIG,
              'host' => DB_HOST_CONFIG,
              'driver' => DB_DRIVER_CONFIG,
              'charset' => 'utf8'
            ]);

            self::$spotInstance = new \Spot\Locator($cfg);
        }
    }

    final public function loadMapper($strEntity)
    {
        if (!isset(self::$spotInstance) || !(self::$spotInstance instanceof \Spot\Locator)) {
            // If invoked from an Addons
            self::__construct(); // init $spotInstance
        }
        return self::$spotInstance->mapper('Particle\Apps\Entities\\'.$strEntity);
    }
}
