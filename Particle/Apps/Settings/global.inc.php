<?php

namespace Particle\Apps;

define('TYPEMODE', 'DEBUG');
define('NAMEHOST', 'host');

/* PATHS */
define('ADDONS_FOLDER', 'Addons');
define('VIEWS_FOLDER', 'Views');
define('ADDONS_PATH', PARTICLE_PATH_APPS.ADDONS_FOLDER.DS);
define('MAPPING_PATH', PARTICLE_PATH_APPS.'Router'.DS.'default.xml');
define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_METHOD', 'index');
define('DEFAULT_LAYOUT', 'default');
define('APPS_PUBLIC_IMG_PATH', PARTICLE_PATH_APPS.'public'.DS.'img'.DS);
define('SMARTY_CACHE_DIR', PARTICLE_PATH_CORE.'tmp'.DS.'cache-smarty'.DS);
define('SMARTY_COMPILE_DIR', PARTICLE_PATH_CORE.'tmp'.DS.'template-compiler-smarty'.DS);
define('SMARTY_CONFIG_DIR', PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.'layout'.DS.DEFAULT_LAYOUT.DS.'configs-smarty'.DS.NAMEHOST.DS);

/* URLs */
define('HOME_URL', 'https://localhost/');
define('HOME_URL_STATIC', 'https://localhost/');
define('BASE_URL_APPS', HOME_URL.FRAMEWORK_FOLDER.DS.APPS_FOLDER.DS);
define('BASE_URL_APPS_STATIC', HOME_URL_STATIC.FRAMEWORK_FOLDER.DS.APPS_FOLDER.DS);

/* DOCTYPE AND CHARSET */
define('CHARSET', 'UTF-8'); // ISO-8859-1
define('DOCTYPE', 'HTML5');  // XHTML / HTML401 /

/* SALT */
define('SALT_CODE', '0000000000000000000000000');

/* SESSION */
define('SESSION_TIME', 10000);
define('SESSION_KEY', '00000000');
define('SESSION_NAME', 'session');

/* COOKIE */
define('COOKIE_KEY', '00000000');

/* LIMIT SECURITY */
define('LIMIT_ALL_SENDMAIL', 400);
define('LIMIT_TO_SENDMAIL', 6);

/* TIME ZONE */
date_default_timezone_set('America/Montevideo');

if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'America/Montevideo');
}
if (!defined('HOSTMAIL')) {
    define('HOSTMAIL', 'localhost');
}
if (!defined('PORTMAIL')) {
    define('PORTMAIL', 25);
}
if (!defined('USERMAIL')) {
    define('USERMAIL', 'mail@localhost.com');
}
if (!defined('PASSMAIL')) {
    define('PASSMAIL', 'root');
}
if (!defined('FROMMAIL')) {
    define('FROMMAIL', 'mail@localhost.com');
}
if (!defined('VarGlobalJS')) {
    define('VarGlobalJS', '
        var VarGlobalJS = "1";
    ');
}
