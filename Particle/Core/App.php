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
    private $_controller = false;
    private $_method = false;
    private $_args = false;
    private $_request = false;
    private $_currentAddons = false;

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
        return $this->_currentAddons;
    }

    final public function getAppController()
    {
        return $this->_controller;
    }

    final public function getAppMethod()
    {
        return $this->_method;
    }

    final public function getAppArgs()
    {
        return $this->_args;
    }

    final public function getAppRequest()
    {
        return $this->_request;
    }

    final public function setAppCurrentAddons($CurrentAddons)
    {
        $this->_currentAddons = $CurrentAddons;
    }

    final public function setAppController($Controller)
    {
        $this->_controller = $Controller;
    }

    final public function setAppMethod($Method)
    {
        $this->_method = $Method;
    }

    final public function setAppArgs($Args)
    {
        $this->_args = $Args;
    }

    final public function setAppRequest($Request)
    {
        $this->_request = $Request;
    }
}
