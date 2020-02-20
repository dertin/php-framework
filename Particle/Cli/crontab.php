<?php
namespace Particle;

use Particle\Core;
use Particle\Apps\Controllers;
use Particle\Apps\Addons;

// Init Framework
require_once dirname(__FILE__).'/../../config.php';

$blnConfigFramework = configFramework();
if (!$blnConfigFramework) {
    echo "err.config";
    return false;
}

require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload.php';
try {
    // Clear SESSION_GC
    Core\Crontab::run();

    // Only PROD
    if (TYPEMODE == 'PROD') {
        if (date("Hi") == "0015" || date("Hi") == "0016") {
            return true;
        }
    }
} catch (\Exception $e) {
      echo 'Exception: '.$e->getMessage();
}
