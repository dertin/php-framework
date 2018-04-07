<?php

/* NOTE: unfinished */

namespace Particle\Core;

use Particle\Core;
use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;

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
    private static $_DBInstance = null;
    private static $_CacheInstance = null;

    protected $_db = null;
    protected $_cache = null;

    public function __construct($loadModelDB = DB_FLAG_SITE, $usecache = CACHE_DB)
    {
        // if the constant 'DB_FLAG_SITE' is false the db module was not configured
        if (DB_FLAG_SITE && $loadModelDB) {

            if (!self::$_DBInstance) {
                self::$_DBInstance = new Core\Database();
            }

            $this->_db = self::$_DBInstance->connectDBPDODefault();

            if(!self::$_CacheInstance){

              // if the constant 'CACHE_DB' is false the cache module was not configured
              if(CACHE_DB && $usecache){

                $CacheDBPATH = CACHE_DB_PATH;

                if(!is_dir($CacheDBPATH)){
                  $CacheDBPATH = sys_get_temp_dir();
                }

                CacheManager::setDefaultConfig([
                  "path" => $CacheDBPATH,
                  "defaultTtl" => CACHE_DB_TIME
                ]);

                self::$_CacheInstance = CacheManager::getInstance('files');
              }

            }

            $this->_cache = self::$_CacheInstance;


        } else {
            $this->_db = null;
            $this->_cache = null;
        }
    }
    
    /* TODO: Review utility */
    public function connectDBNew($dbHost, $dbName, $dbUser, $dbPass, $dbChar)
    {
        $this->_db = self::$_DBInstance->connectDBPDONew($dbHost, $dbName, $dbUser, $dbPass, $dbChar);
    }
    /* TODO: Review utility */
    public function connectDBDefault()
    {
        $this->_db = self::$_DBInstance->connectDBPDODefault();
    }
    /* TODO: Review utility */
    public function closeDB()
    {
        $this->_db = self::$_DBInstance->closeDBPDO();
    }
}
