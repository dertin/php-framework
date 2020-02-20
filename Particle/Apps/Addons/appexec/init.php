<?php

namespace Particle\Apps\Addons;

use Particle\Core;

class appexecAddons extends Core\Controller
{
    // private $viewAddons;

    public function __construct()
    {
        /*setting addons*/
        $nameAddons = 'appexec';
        Core\App::getInstance()->setAppCurrentAddons($nameAddons);
        /* end setting addons*/
    }

    public function appExec($dir, $returnString = false)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, HOME_URL.$dir);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($returnString) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return string
        } else {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0); // print buffer
        }

        $dataString = curl_exec($ch);

        curl_close($ch);

        if ($returnString) {
            return $dataString; // return string
        } else {
            @ob_clean(); // clear buffer
        }
    }
}
