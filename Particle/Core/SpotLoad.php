<?php

namespace Particle\Core;

use Particle\Core;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

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
    protected static $spotCache = null;

    protected $cache = null;

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
        if (!isset(self::$spotCache)) {
            CacheManager::setDefaultConfig(new ConfigurationOption([
              'path' => sys_get_temp_dir(), // or in windows "C:/tmp/"
            ]));
            self::$spotCache = CacheManager::getInstance('files');
            // CacheManager::setDefaultConfig([
            //   "path" => '/home/g203906/securecache',
            // ]);
            // self::$spotCache = @CacheManager::getInstance('files');
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
