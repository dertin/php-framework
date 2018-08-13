<?php

namespace Particle\Apps\Views;

use Particle\Core;

class assignLayout extends Core\Controller
{
    public function __construct()
    {
        $noLoadView = true;
        parent::__construct($noLoadView);
    }

    public function procesar(): array
    {
        // return array(
        //     'testAssignLayoutOne' => '1',
        //     'testAssignLayoutTwo' => '2'
        // );
        return array();
    }
}
