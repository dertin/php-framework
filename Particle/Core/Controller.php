<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @name Controller
 *
 *  @category Particle\Core
 *
 *  @author dertin
 *  @abstract
 **/

abstract class Controller
{
    protected $_view = null;
    protected $_args = null;

    private static $currentAddons;
    private static $controller;
    private static $method;

    public function __construct()
    {
        self::$controller = Core\App::getInstance()->getAppController();
        self::$method = Core\App::getInstance()->getAppMethod();

        $this->_args = Core\App::getInstance()->getAppArgs();

        $cacheKey = Core\App::getInstance()->getAppRequest(); // $cacheKey

        $this->_view = new \Particle\Core\View(self::$controller, self::$method, $cacheKey, false);

    }

    final protected static function loadViewAddons()
    {

        self::$currentAddons = Core\App::getInstance()->getAppCurrentAddons();

        return new \Particle\Core\View(self::$currentAddons, 'index', false, true);
    }

    final protected static function loadAddons($addons_name = false, $noException = false)
    {
        $pathPluginInit = ADDONS_PATH.$addons_name.DS.'init.php';

        if (is_readable($pathPluginInit)) {
            require_once $pathPluginInit;

            $classPlugin = $addons_name.'Addons';

            $pluginNamesapce = 'Particle\\Apps\\Addons\\'.$classPlugin;

            if (class_exists($pluginNamesapce, false)) {
                $pluginInstance = new $pluginNamesapce();

                return $pluginInstance;
            } else {
                if (!$noException) {
                    throw new \Exception('Bad Class Name Addons');
                } else {
                    return false;
                }
            }
        } else {
            if (!$noException) {
                throw new \Exception('Error Addons');
            } else {
                return false;
            }
        }
    }

    final protected static function redirect($path = false, $external = false)
    {
        if ($path) {
            if ($external) {
                header('location:'.$path);
                exit;
            } else {
              header('location:'.HOME_URL.$path);
              exit;
            }
        } else {
          header('location:'.HOME_URL);
          exit;
        }
        throw new \Exception('Error Redirect');
        return false;
    }

    final protected static function getText($key, $array = null, $default = '', $removeHtml = false, $filterXSS = false)
    {
        if (is_null($array)) {
            $array = $_REQUEST;
        }

        if (isset($array[$key]) && !empty($array[$key])) {
            if ($filterXSS) {
                $filterText = Core\Security::filterXSS($array[$key], $default);
            } elseif ($removeHtml != 'html' && is_bool($removeHtml)) {
                $filterText = Core\Security::cleanHtml($array[$key], $default, $removeHtml);
            } else {
                $filterText = $array[$key];
            }

            return $filterText;
        }

        return $default;
    }

    final protected static function getInt($key, $array = null, $default = 0)
    {
        if (is_null($array)) {
            $array = $_POST;
        }

        if (isset($array[$key]) && !empty($array[$key])) {
            if ($array != $_SESSION) {
                if ($array == $_GET) {
                    $input = INPUT_GET;
                } elseif ($array == $_POST) {
                    $input = INPUT_POST;
                } elseif ($array == $_COOKIE) {
                    $input = INPUT_COOKIE;
                } else {
                    return false;
                }

                $array[$key] = filter_input($input, $key, FILTER_VALIDATE_INT);
            }
            $filterInt = Core\Security::filterInt($array[$key], $default);

            return $filterInt;
        }

        return 0;
    }

    final protected static function getParam($key, $array = null, $default = false)
    {
        if (is_null($array)) {
            $array = $_REQUEST;
        }

        if (isset($array[$key])) {
            return $array[$key];
        } else {
            return $default;
        }
    }

    final protected static function getSql($key, $array = null, $html = true, $default = false)
    {
        if (is_null($array)) {
            $array = $_REQUEST;
        }
        if (isset($array[$key]) && !empty($array[$key])) {
            return Core\Security::filterSql($array[$key], $html, $default);
        } else {
            return $default;
        }
    }

    final protected static function getAlphaNum($key, $array = null, $default = '')
    {
        if (is_null($array)) {
            $array = $_REQUEST;
        }

        if (isset($array[$key]) && !empty($array[$key])) {
            return Core\Security::filterAlphaNum($array[$key], $default);
        } else {
            return $default;
        }
    }

    final protected static function self_to_utf8($string)
    {
        if (!mb_check_encoding($string, 'UTF-8')) {
            $fromChar = mb_detect_encoding($string);
            $string = mb_convert_encoding($string, 'UTF-8', $fromChar);
        }

        return $string;
    }

    final protected static function uploadFile($File, $FullPatch, $allowedExts = false, $allowedTypes = false)
    {
        if (!$allowedExts) {
            $allowedExts = array('jpg', 'jpeg', 'gif', 'png', 'zip');
        }

        if (!$allowedTypes) {
            $allowedTypes = array('image/gif', 'image/jpeg', 'image/pjpeg');
        }

        $extension = end(explode('.', $File['name']));
        // TODO: add $allowedTypes
        if (($File['size'] < 20000) && in_array($extension, $allowedExts)) {
            if ($File['error'] > 0) {
                echo 'Return Code: '.$File['error'].'<br />';
            } else {
                echo 'Upload: '.$File['name'].'<br />';
                echo 'Type: '.$File['type'].'<br />';
                echo 'Size: '.($File['size'] / 1024).' Kb<br />';
                echo 'Temp file: '.$File['tmp_name'].'<br />';

                if (file_exists($FullPatch.$File['name'])) {
                    echo $File['name'].' already exists. ';
                } else {
                    move_uploaded_file($File['tmp_name'], $FullPatch.$File['name']);
                    echo 'Stored in: '.$FullPatch.$File['name'];

                    return $FullPatch.$File['name'];
                }
            }
        } else {
            echo 'Error - Invalid File';
        }
    }

    final protected static function check_in_range($start_date, $end_date, $evaluame)
    {
        // data format YYYY-mm-dd

        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($evaluame);
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }

}
