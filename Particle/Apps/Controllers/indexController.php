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
        $spot = parent::$spotInstance;

        $aTest = Particle\Apps\Entities\Person::fields();
        var_dump($aTest);

        $personMapper = $spot->mapper('Particle\Apps\Entities\Person');
        $personMapper->migrate();
        $this->view->show();
    }
}
