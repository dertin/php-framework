<?php

namespace Particle\Apps;

use Particle\Core;

class _SAMPLE_Controller extends Core\Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Set resources
        $this->view->setCssJsLayout('default', 'default');
        $this->view->setJsExternal(array('https://maps.google.com/maps/api/js?key='), false);
        $this->view->setCssImages(array('home.images'), false);
        $this->view->setCss(array('home'), false);
        $this->view->createTmpCss();
        $this->view->setJs(array('home'), false);
        $this->view->createTmpJs();

        // Spot ORM - http://phpdatamapper.com/
        $mapperTesting = $this->loadMapper('Testing');
        $arrTesting = $mapperTesting->all()->toArray();
        
        // Smarty
        $this->view->assign('hello', 'Hello Word');
        $this->view->show();

        return true;
    }
}
