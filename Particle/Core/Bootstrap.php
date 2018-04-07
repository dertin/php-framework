<?php

namespace Particle\Core;

/**
 *  @name Bootstrap
 *
 *  @category Particle\Core
 *
 *  @author dertin
 *
 *
 *  Esta class se encarga de arrancar nuestro framework
 *  en base a lo obtenido de la class Request,
 *  realiza la carga del controller, method y args.
 **/
final class Bootstrap
{
    public static function run(Request $Request)
    {
        $controllerName = $Request->getControlador();

        if (empty($controllerName)) {
            throw new \Exception('Bad controller');
        }

        $controllerNameClass = $controllerName.'Controller';

        $rutaControlador = PARTICLE_PATH_APPS.'Controllers'.DS.$controllerNameClass.'.php';
        if (!is_readable($rutaControlador)) {
          foreach(glob(PARTICLE_PATH_APPS.'Controllers'.DS.'*', GLOB_ONLYDIR) as $dir) {
            $subDirController = basename($dir);
            $rutaControlador = PARTICLE_PATH_APPS.'Controllers'.DS.$subDirController.DS.$controllerNameClass.'.php';
            if (is_readable($rutaControlador)) {
              break;
            }
          }
        }

        $controllerClass = 'Particle\\Apps\\Controllers\\'.$controllerNameClass;

        $method = $Request->getMetodo();

        $args = $Request->getArgs();

        if (is_readable($rutaControlador)) {
            require_once $rutaControlador;

            if (class_exists($controllerClass)) {
                if (!isset($args) || empty($args)) {
                    $args = false;
                }

                if (is_string($args)) {
                    $args = (array) $args;
                }
                // Set APP

                App::getInstance()->setAppController($controllerName);

                if (!method_exists($controllerClass, $method)) {
                    if ($controllerName === DEFAULT_CONTROLLER && empty($method)) {
                        $method = DEFAULT_METHOD;
                    } elseif (!STRICT_ROUTING) {
                        $method = 'index';
                    } else {
                        throw new \Exception('Bad Method');
                    }
                }

                App::getInstance()->setAppMethod($method);
                App::getInstance()->setAppArgs($args);
                App::getInstance()->setAppRequest($Request->getRequest());

                $controller = new $controllerClass();
            } else {
                throw new \Exception('Bad Controller');
            }

            $aCall = array($controller, $method);

            if (!is_callable($aCall)) {
                throw new \Exception('Error Call');
            }

            // Call Controller
            if ($args) {
                call_user_func_array($aCall, $args);
            } else {
                call_user_func($aCall);
            }
        } else {
            throw new \Exception('ErrorPathController');
        }
    }
}
