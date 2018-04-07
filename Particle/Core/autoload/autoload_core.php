<?php

if (!defined('ROOT')) {
  return false;
}

/**
 * Autoload de Framework Particle\Core
 */
spl_autoload_register(function ($ClassNameWithNameSpace) {

    $namespace = 'Particle\Core';

    if(substr($ClassNameWithNameSpace, 0, strlen($namespace)) === $namespace){
      $fileClass = ROOT . str_replace('\\', '/', $ClassNameWithNameSpace) . '.php';
      if (file_exists($fileClass)) {
        require_once $fileClass;
      }else{
        return false;
      }
    }

    return true;
});
