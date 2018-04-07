<?php

// Autoload namespace Particle\Core
require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload_core.php';

// Autoload composer Core (Librerias de terceros para Framework Core)
require_once PARTICLE_PATH_CORE.'vendor'.DS.'autoload.php';

// Autoload composer Apps (Librerias de terceros para Aplicacion)
require_once PARTICLE_PATH_APPS.'vendor'.DS.'autoload.php';

// Autoload namespace Particle\Apps (Controllers And Entities)
require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload_apps.php';
