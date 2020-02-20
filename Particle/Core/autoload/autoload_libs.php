<?php

if (!defined('PARTICLE_PATH_APPS')) {
    return false;
}

spl_autoload_register(function ($classNameWithNameSpace) {
    $namespace = 'Libs';

    if (substr($classNameWithNameSpace, 0, strlen($namespace)) === $namespace) {
        $classPath = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, PARTICLE_PATH_APPS.$classNameWithNameSpace.'.php');
        // Get file real path
        if (false === ( $classPath = realpath($classPath) )) {
            // File not found
            return false;
        } else {
            require_once($classPath);
            return true;
        }
    }
});
