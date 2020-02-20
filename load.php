<?php

namespace Particle;

use Particle\Core;

ob_start();
ob_implicit_flush(0);

error_reporting(E_ALL);  // Add strict
ini_set('display_errors', '1');  // mostrar errores

try {
    // Constants configuration
    require_once 'config.php';

    $blnConfigFramework = configFramework();
    if (!$blnConfigFramework) {
        throw new \Exception('UnexpectedConfig');
    }

    // Autoload
    require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload.php';

    if (session_status() == PHP_SESSION_NONE) {
         //session_start();
         Core\Session::singleton();
    }

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

        if ($srtExceptionMessage === 'ErrorPathController' || $srtExceptionMessage === 'Error404') {
            $sCurrURLFull = Core\Debug::getCurrentUrl();
            $sCurrURLExt = substr(strrchr($sCurrURLFull, '.'), 1);

            //Core\Debug::savelogfile(404, $e->getFile().$e->getLine(), 'NotFound: '.$sCurrURLFull);

            header('HTTP/1.1 404 Not Found', true, 404);

            /* NOTE: BE CAREFUL YOU CAN GENERATE A LOOP*/
            if (is_readable(ROOT.'error.html')) {
                if ($sCurrURLExt != 'xml' && $sCurrURLExt != 'webp' && $sCurrURLExt != 'ico' && $sCurrURLExt != 'png' && $sCurrURLExt != 'jpg' && $sCurrURLExt != 'gif' && $sCurrURLExt != 'js' && $sCurrURLExt != 'css') {
                    $html404 = file_get_contents(ROOT.'error.html');
                    echo $html404;
                }
            }
        } elseif ($srtExceptionMessage != 'UnexpectedConfig') {
            Core\Debug::savelogfile(500, $e->getFile().$e->getLine(), $srtExceptionMessage);

            header('HTTP/1.1 500 Internal Server Error', true, 500);

            if (is_readable(ROOT.'error-interno.html')) {
                  $html500 = file_get_contents(ROOT.'error-interno.html');
                  echo $html500;
            }
        } else {
            header('HTTP/1.1 400 Bad request', true, 400);
            echo 'Fatal error!';
        }
    } catch (\Exception $e) {
        header('HTTP/1.1 400 Bad request', true, 400);
        echo 'Fatal error!';
    }

    exit();
}
