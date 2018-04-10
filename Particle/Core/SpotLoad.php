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

abstract class SpotLoad
{
    protected static $spotInstance = null;

    public function __construct()
    {
        if (!isset($spotInstance) || !($spotInstance instanceof Spot\Locator)) {
            $cfg = new \Spot\Config();

            $cfg->addConnection(DB_TYPE_CONFIG, [
              'dbname' => DB_NAME_CONFIG,
              'user' => DB_USER_CONFIG,
              'password' => DB_PASS_CONFIG,
              'host' => DB_HOST_CONFIG,
              'driver' => DB_DRIVER_CONFIG,
            ]);

            $spotInstance = new \Spot\Locator($cfg);
        }
    }
}
