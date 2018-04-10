<?php
/**
 * Test Constants
 */
if (!defined('PARTICLE_PATH_APPS')) {
    return false;
}

/**
 * Autoload de Entidades de base de datos con Spot ORM
 */
spl_autoload_register(function ($ClassNameWithNameSpace) {
    $namespace = 'Particle\Apps\Entities\\';

    if (substr($ClassNameWithNameSpace, 0, strlen($namespace)) === $namespace) {
        $nameClass = substr($ClassNameWithNameSpace, strlen($namespace));

        $filePath = PARTICLE_PATH_APPS.'Entities'.DS.$nameClass.'.php';
        
        if (!is_readable($filePath)) {
            foreach (glob(PARTICLE_PATH_APPS.'Entities'.DS.'*', GLOB_ONLYDIR) as $dir) {
                $subDir= basename($dir);
                $filePath = PARTICLE_PATH_APPS.'Entities'.DS.$subDir.DS.$nameClass.'.php';
                if (is_readable($filePath)) {
                    require_once $filePath;
                    break;
                }
            }
        } else {
            require_once $filePath;
        }
    }
    return true;
});

/**
 * Autoload de Controllers
 */
spl_autoload_register(function ($ClassNameWithNameSpace) {
    $namespace = 'Particle\Apps\Controllers\\';

    if (substr($ClassNameWithNameSpace, 0, strlen($namespace)) === $namespace) {
        $nameClass = substr($ClassNameWithNameSpace, strlen($namespace));

        $filePath = PARTICLE_PATH_APPS.'Controllers'.DS.$nameClass.'.php';

        if (!is_readable($filePath)) {
            foreach (glob(PARTICLE_PATH_APPS.'Controllers'.DS.'*', GLOB_ONLYDIR) as $dir) {
                $subDir= basename($dir);
                $filePath = PARTICLE_PATH_APPS.'Controllers'.DS.$subDir.DS.$nameClass.'.php';
                if (is_readable($filePath)) {
                    require_once $filePath;
                    break;
                }
            }
        } else {
            require_once $filePath;
        }
    }
    return true;
});
