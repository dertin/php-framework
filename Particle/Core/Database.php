<?php

namespace Particle\Core;

/**
 *  @name Database
 *
 *  @category Particle\Core
 *
 *  @author dertin
 **/
final class Database
{
    private static $_PDOInstance = null;
    private static $_PDOConnect = null;

    public function connectDBPDODefault()
    {
        $PDOConnectNew = DB_HOST_SITE.DB_NAME_SITE.DB_USER_SITE.DB_PASS_SITE.DB_CHAR_SITE;

        if (self::$_PDOConnect != $PDOConnectNew) {
            return self::_connectDBPDO();
        } else {
            return self::$_PDOInstance;
        }
    }

    public function connectDBPDONew($dbHost, $dbName, $dbUser, $dbPass, $dbChar)
    {
        $PDOConnectNew = $dbHost.$dbName.$dbUser.$dbPass.$dbChar;

        if (self::$_PDOConnect != $PDOConnectNew) {
            return self::_connectDBPDO($dbHost, $dbName, $dbUser, $dbPass, $dbChar);
        } else {
            return self::$_PDOInstance;
        }
    }

    private function _connectDBPDO($dbHost = DB_HOST_SITE, $dbName = DB_NAME_SITE, $dbUser = DB_USER_SITE, $dbPass = DB_PASS_SITE, $dbChar = DB_CHAR_SITE)
    {
        try {
            self::$_PDOInstance = null;

            $dsn = DB_TYPE_SITE.':host='.$dbHost.';dbname='.$dbName;

            $driver_options = array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$dbChar);

            self::$_PDOInstance = new \PDO($dsn, $dbUser, $dbPass, $driver_options);
            self::$_PDOInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$_PDOConnect = $dbHost.$dbName.$dbUser.$dbPass.$dbChar;
        } catch (\PDOException $e) {
            die('PDO CONNECTION ERROR: '.$e->getMessage().'<br/>');
        }

        return self::$_PDOInstance;
    }

    public function closeDBPDO()
    {
        self::$_PDOInstance = null;
        self::$_PDOConnect = null;

        return;
    }
}
