<?php

namespace Particle;

ob_start();
ob_implicit_flush(0);

error_reporting(E_ALL);  // Add strict
ini_set('display_errors', '1');  // mostrar errores

// Constants configuration
require_once 'config.php';

// Autoload
require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload.php';

try {
    if (OUTPUT_CONTROL) {
        // Get Unexpected output //
        $unexpected_output = ob_get_contents();

        ob_clean();

        // Check Unexpected output //
        if ($unexpected_output) {
            Core\Debug::savelogfile(1, 'Unexpected output', $unexpected_output);
            throw new \Exception('Unexpected output');
        }
    }

    // Run Core //
    Core\Bootstrap::run(new Core\Request());

    // Output //
    ob_end_flush();

    exit();
} catch (\Exception $e) {
    try {
        $srtExceptionMessage = (string) $e->getMessage();

        if ($srtExceptionMessage === 'ErrorPathController') {
            $sCurrURLFull = Core\Debug::getCurrentUrl();
            $sCurrURLExt = substr(strrchr($sCurrURLFull, '.'), 1);

            //Core\Debug::savelogfile(404, $e->getFile().$e->getLine(), 'NotFound: '.$sCurrURLFull);

            header('HTTP/1.1 404 Not Found', true, 404);
            header("Status: 404 Not Found");

        /*
          NOTE: BE CAREFUL YOU CAN GENERATE A LOOP

          if($sCurrURLExt != 'ico' && $sCurrURLExt != 'png' && $sCurrURLExt != 'jpg' && $sCurrURLExt != 'gif' && $sCurrURLExt != 'js' && $sCurrURLExt != 'css'){
            $html404 = file_get_contents(HOME_URL.'info/e404');
            echo $html404;
          }
        */
        } else {
            Core\Debug::savelogfile(500, $e->getFile().$e->getLine(), $srtExceptionMessage);
            header('HTTP/1.1 500 Internal Server Error', true, 500);
            /*
              NOTE: BE CAREFUL YOU CAN GENERATE A LOOP

              header('Location: '.HOME_URL.'info/e500');
            */
        }
    } catch (\Exception $e) {
        echo 'Fatal error, no error log generated!';
    }

    exit();
}
