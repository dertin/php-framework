<?php

namespace Particle\Core;

/**
 *  @name App
 *
 *  @category Particle\Core
 *
 *  @author dertin
 *
 *
 *  Esta class se encarga de contener informaciÃ³n sobre
 *  el controller, method y args en proceso.
 **/
final class App
{
    private $controller = false;
    private $method = false;
    private $args = false;
    private $request = false;
    private $currentAddons = false;

    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    final public function getAppCurrentAddons()
    {
        return $this->currentAddons;
    }

    final public function getAppController()
    {
        return $this->controller;
    }

    final public function getAppMethod()
    {
        return $this->method;
    }

    final public function getAppArgs()
    {
        return $this->args;
    }

    final public function getAppRequest()
    {
        return $this->request;
    }

    final public function setAppCurrentAddons($CurrentAddons)
    {
        $this->currentAddons = $CurrentAddons;
    }

    final public function setAppController($Controller)
    {
        $this->controller = $Controller;
    }

    final public function setAppMethod($Method)
    {
        $this->method = $Method;
    }

    final public function setAppArgs($Args)
    {
        $this->args = $Args;
    }

    final public function setAppRequest($Request)
    {
        $this->request = $Request;
    }
}
