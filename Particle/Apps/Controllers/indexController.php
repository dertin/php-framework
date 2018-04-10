<?php

namespace Particle\Apps\Controllers;

use Particle\Core;
use Particle\Apps\Entities;

class indexController extends Core\Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $spot = parent::$spotInstance;
        $personMapper = $spot->mapper('Particle\Apps\Entities\Person');
        $personMapper->migrate();
        $this->view->show();
    }
}
