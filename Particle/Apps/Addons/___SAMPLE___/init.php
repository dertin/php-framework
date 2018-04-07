<?php

namespace Particle\Apps\Addons;

use Particle\Core;

class ___SAMPLE___Addons extends Core\Controller
{
    private $_viewAddons;

    public function __construct()
    {
        /*setting addons*/

        $nameAddons = '___SAMPLE___';

        Core\App::getInstance()->setAppCurrentAddons($nameAddons);

        $this->_viewAddons = parent::loadViewAddons();

        /* end setting addons*/
    }

    /*
        Call to Addons:

        $objAddons = parent::loadAddons('nameaddons');
        $objAddons->func();

    */

    public function sample()
    {
        $var = 'Hello World';

        return $var;
    }
}
