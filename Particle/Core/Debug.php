<?php

namespace Particle\Core;

/**
 *  @name Debug
 *
 *  @category Particle\Core
 *
 *  @author dertin
 **/
final class Debug
{
    final public static function savelogfile($id, $location, $message, $filename = 'error.log')
    {
        //
        // id: Error ID
        // location: Location of the file and line eg. index.php:20
        // message: Error message
        // filename: Log file path
        //
        // Usage: Core\Debug::savelogfile(1, 'index.php:33', 'Mensaje de Error');
        //
        // TODO: ultimo parametro $filename sin utilizar
        //
        $filename = $_SERVER['DOCUMENT_ROOT'].'/error.log';

        $file = fopen($filename, 'a');

        if (!is_string($location) || !is_string($message)) {
            throw new \Exception('Incorrect datatype for save log');
        }
        $ip = Security::getIp();
        fwrite($file, '['.date('Y-m-d H:i:s')."][$ip][ERROR $id][$location][$message]".PHP_EOL);

        fclose($file);
    }

    /* TODO: file log: Add func -> contar cantidad de logs; borrar logs; buscar en archivo de log */

    final public static function inspect($var, $exit = false)
    {

        // Usage: Core\Debug::inspect($holaVar);

        $btr = debug_backtrace();

        $line = $btr[0]['line'];

        $file = basename($btr[0]['file']);

        echo '<br><pre>';

        echo 'File:'.$file.':'.$line.'<br>';

        if (is_array($var) || is_object($var)) {
            echo htmlentities(print_r($var, true));
        } elseif (is_string($var)) {
            echo 'string('.strlen($var).') \''.htmlentities($var).'\'<br>';
        } else {
            var_dump($var);
        }
        echo '</pre>';

        if ($exit) {
            exit;
        }
    }

    final public static function getCurrentUrl($strip = true)
    {
        // filter function
        $filter = function ($input, $strip) {
            $input = urldecode($input);
            $input = str_ireplace(array("\0", '%00', "\x0a", '%0a', "\x1a", '%1a'), '', $input);
            if ($strip) {
                $input = strip_tags($input);
            }
            $input = htmlentities($input, ENT_QUOTES, 'UTF-8'); // or whatever encoding you use...
            return trim($input);
        };

        $url = array();
        // set protocol
        $url['protocol'] = 'http://';
        if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) === 'on' || $_SERVER['HTTPS'] == 1)) {
            $url['protocol'] = 'https://';
        } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            $url['protocol'] = 'https://';
        }
        // set host
        $url['host'] = $_SERVER['HTTP_HOST'];
        // set request uri in a secure way
        $url['request_uri'] = $filter($_SERVER['REQUEST_URI'], $strip);

        return implode('', $url);
    }
}
