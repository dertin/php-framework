<?php

namespace Particle\Apps\Controllers;

use Particle\Core;

class indexController extends Core\Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $personMapper = $this->spot->mapper('\Particle\Apps\Entities\Person');
        $personMapper->migrate();

        $this->view->show();
    }
}
