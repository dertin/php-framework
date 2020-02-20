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

abstract class Controller extends Core\SpotLoad
{
    protected $view = null;
    protected $args = null;
    protected $spot = null;

    private $currentAddons;
    private $controller;
    private $method;

    public function __construct($noLoadView = false)
    {
        parent::__construct(); // init $spotInstance
        //$this->spot = parent::$spotInstance;

        $this->controller = Core\App::getInstance()->getAppController();
        $this->method = Core\App::getInstance()->getAppMethod();

        $this->args = Core\App::getInstance()->getAppArgs();

        $cacheKey = Core\App::getInstance()->getAppRequest(); // $cacheKey

        if (!$noLoadView) {
            $this->view = new \Particle\Core\View($this->controller, $this->method, $cacheKey, false);
        }
    }

    final protected function loadViewAddons()
    {
        $this->currentAddons = Core\App::getInstance()->getAppCurrentAddons();
        return new \Particle\Core\View($this->currentAddons, 'index', false, true);
    }

    final protected function loadAddons($addons_name = false, $noException = false)
    {
        $classPlugin = $addons_name.'Addons';

        $pluginNamesapce = 'Particle\\Apps\\Addons\\'.$classPlugin;
        $pluginInstance = new $pluginNamesapce();

        if ($pluginInstance instanceof $pluginNamesapce) {
            return $pluginInstance;
        } else {
            if (!$noException) {
                throw new \Exception('Bad Class Name Addons');
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
            // TODO: check this
            // if (!isset($_SESSION) || (isset($_SESSION) && $array != $_SESSION)) {
            //     if ($array == $_GET) {
            //         $input = INPUT_GET;
            //     } elseif ($array == $_POST) {
            //         $input = INPUT_POST;
            //     } elseif ($array == $_COOKIE) {
            //         $input = INPUT_COOKIE;
            //     } else {
            //         return false;
            //     }
            //     $array[$key] = filter_input($input, $key, FILTER_VALIDATE_INT); // FILTER_SANITIZE_NUMBER_INT
            // }
            $filterInt = Core\Security::filterInt($array[$key], $default);
            return $filterInt;
        }
        return $default;
    }

    final protected static function getParam($key, $array = null, $default = false, $filterValidate = null)
    {
        if (is_null($array)) {
            $array = $_REQUEST;
        }

        if (isset($array[$key])) {
            if (!empty($filterValidate)) {
                if (!filter_var($array[$key], $filterValidate)) {
                    return $default;
                }
            }
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

    final protected static function selfToUTF8($string)
    {
        if (!mb_check_encoding($string, 'UTF-8')) {
            $fromChar = mb_detect_encoding($string);
            $string = mb_convert_encoding($string, 'UTF-8', $fromChar);
        }

        return $string;
    }
}
