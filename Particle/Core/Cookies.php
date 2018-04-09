<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @name Cookies
 *
 *  @category Particle\Core
 *
 *  @author dertin
 *  @see https://www.ietf.org/rfc/rfc2965.txt
 */
final class Cookies
{

    /**
     * Reads a cookie and returns its value
     * @param string $name Name of the cookie
     * @return mixed Value of the cookie
     */
    public static function read($name)
    {
        if (isset($_COOKIE[$name]) && !empty($_COOKIE[$name])) {
            $valueDecrypt = Core\Security::decrypt((string)$_COOKIE[$name], COOKIE_KEY, SALT_CODE);
            return $valueDecrypt;
        }
        return null;
    }

    /**
     * Creates or modify a cookie
     *
     * @param string $name Name of the cookie
     * @param string $value Value of the cookie. Destroy the cookie if omitted or null
     * @param int $duration Life time of the cookie. Uses default value if omitted or null
     */
    public static function write($name, $value = null, $duration = 0)
    {
        if (!isset($value)) {
            return self::delete($name);
        }

        $domain=ini_get('session.cookie_domain');
        $path=ini_get('session.cookie_path');
        $secure=isset($_SERVER['HTTPS']);
        $httponly=true;

        // Expiration date from the life time in seconds
        if ($duration==0) {
            $expire = 0;
        } else {
            $expire = time()+((int) $duration);
        }

        $valueEncrypt = Core\Security::encrypt((string)$value, COOKIE_KEY, SALT_CODE);

        // Writes the cookie
        $bWriteCookie = setcookie($name, $valueEncrypt, $expire, $path, $domain, $secure, $httponly);

        if ($bWriteCookie === true) {
            $_COOKIE[$name] = $valueEncrypt;
            return true;
        }

        return false;
    }

    /**
     * Deletes a cookie
     *
     * @param string $name Name of the cookie
     */
    public static function delete($name)
    {
        $domain=ini_get('session.cookie_domain');
        $path=ini_get('session.cookie_path');
        $secure=isset($_SERVER['HTTPS']);
        $httponly=true;
        $expire = time()-3600*30;
        $valueNull = '';

        $bWriteCookie = setcookie($name, $valueNull, $expire, $path, $domain, $secure, $httponly);

        if ($bWriteCookie === true) {
            $_COOKIE[$name] = $valueNull;
            return true;
        }

        return false;
    }
}
