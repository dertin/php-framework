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
        $personMapper = $this->$spotInstance->mapper('Entities/Person');
        $personMapper->migrate();
        $this->view->show();
    }
}
