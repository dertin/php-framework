<?php

namespace Particle\Apps;

use Particle\Core;

class indexController extends Core\Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->_view->show(false, false, false, 'full', true);
    }
}
