<?php
namespace Particle;

// Init Framework
require_once dirname(__FILE__).'/../../config.php';
configFramework();
require_once PARTICLE_PATH_CORE.'autoload'.DS.'autoload.php';

Core\Crontab::run();
