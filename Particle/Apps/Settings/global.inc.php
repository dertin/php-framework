<?php

namespace Particle\Apps;

define('TYPEMODE', 'DEBUG');
define('NAMEHOST', 'host');
define('SITENAME', 'Site.com');

/* PATHS */
define('ADDONS_FOLDER', 'Addons');
define('VIEWS_FOLDER', 'Views');
define('ADDONS_PATH', PARTICLE_PATH_APPS.ADDONS_FOLDER.DS);
define('MAPPING_PATH', PARTICLE_PATH_APPS.'Router'.DS.'default.xml');
define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_METHOD', 'index');
define('DEFAULT_LAYOUT', 'default');
define('PUBLIC_IMG_PATH', PUBLIC_PATH.'images'.DS);
define('SMARTY_CACHE_DIR', PARTICLE_PATH_CORE.'tmp'.DS.'cache-smarty'.DS);
define('SMARTY_COMPILE_DIR', PARTICLE_PATH_CORE.'tmp'.DS.'template-compiler-smarty'.DS);
define('SMARTY_CONFIG_DIR', PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.'layout'.DS.DEFAULT_LAYOUT.DS.'configs-smarty'.DS.NAMEHOST.DS);

/* URLs */
define('HOME_URL', 'https://www.site.com/');
define('HOME_URL_STATIC', 'https://www.site.com/');
define('BASE_URL_APPS', HOME_URL.FRAMEWORK_FOLDER.DS.APPS_FOLDER.DS);
define('BASE_URL_APPS_STATIC', HOME_URL_STATIC.FRAMEWORK_FOLDER.DS.APPS_FOLDER.DS);
define('PUBLIC_URL', HOME_URL.'public'.DS);
define('PUBLIC_URL_STATIC', HOME_URL_STATIC.'public'.DS);

/* DOCTYPE AND CHARSET */
define('CHARSET', 'UTF-8'); // ISO-8859-1
define('DOCTYPE', 'HTML5');  // XHTML / HTML401 /

/* SALT */
define('SALT_CODE', '0000000000000000000000000');

/* SESSION */
define('SESSION_TIME', 3600); // en segundos
define('SESSION_KEY', '00000000');
define('SESSION_NAME', 'stf');
define('SESSION_GC', 'default'); // crontab or default
define('SESSION_GC_TIME', 300); // only for GC crontab

/* COOKIE */
define('COOKIE_KEY', '00000000'); // codigo para encriptar cookie- requiere de SALT_CODE
define('COOKIE_NAME_LISTA_PROVEEDORES', 'lp'); // nombre de cookie para lista de proveedores
define('COOKIE_TIME_LISTA_PROVEEDORES', 0); // tiempo de vida de lista de proveedores
define('COOKIE_LIMIT_LISTA_PROVEEDORES', 200); // maximo proveedores para cotizar por solicitud
define('COOKIE_LIMIT_FORM_LISTA_PROVEEDORES', 15); // maxima informacion de solicitud para procesar
define('COOKIE_NAME_RESULT', 'lr'); // nombre de cookie con lista de FichaId segun resultados actual de listado
define('COOKIE_TIME_RESULT', 0);
/* LIMIT SECURITY */
define('LIMIT_ALL_SENDMAIL', 400);
define('LIMIT_TO_SENDMAIL', 6);

/* TIME ZONE */
date_default_timezone_set('America/Montevideo');

if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'America/Montevideo');
}
if (!defined('HOSTMAIL')) {
    define('HOSTMAIL', 'smtp.site.com');
}
if (!defined('PORTMAIL')) {
    define('PORTMAIL', 587);
}
if (!defined('USERMAIL')) {
    define('USERMAIL', 'info@site.com');
}
if (!defined('PASSMAIL')) {
    define('PASSMAIL', '123456789');
}
if (!defined('FROMNAME')) {
    define('FROMNAME', 'Site');
}
if (!defined('FROMMAIL')) {
    define('FROMMAIL', 'info@site.com');
}
if (!defined('VARGLOBALJS')) {
    define('VARGLOBALJS', 'var PUBLIC_URL = "'.PUBLIC_URL_STATIC.'"; var HOME_URL = "'.HOME_URL.'";');
}
/* Custom constants */
