<?php

function configFramework(): bool
{
    /* CONFIG */
    define('DS', DIRECTORY_SEPARATOR);
    define('ROOT', realpath(dirname(__FILE__)).DS);
    define('FRAMEWORK_FOLDER', 'Particle');
    define('CORE_FOLDER', 'Core');
    define('PARTICLE_PATH', ROOT.FRAMEWORK_FOLDER.DS);
    define('PARTICLE_PATH_CORE', PARTICLE_PATH.CORE_FOLDER.DS);
    define('APPS_FOLDER', 'Apps');
    define('PARTICLE_PATH_APPS', PARTICLE_PATH.APPS_FOLDER.DS);
    define('PUBLIC_PATH', ROOT.'public'.DS);
    define('PRIVATE_PATH', ROOT.'private'.DS);
    
    /* Indicate the user group of the system where your application is running. */
    define('WWW_GRP', 'www-data');

    /* ROUTING */
    define('DEFAULT_ROUTING', true);
    define('MAPPING_ROUTING', true);
    define('STRICT_ROUTING', false);

    return configApps();
}

function prepareSysTempDir($strSysTempDir): void
{
    // $old_umask = umask(0);

    // Application temporary directory
    $strSysTempDirApp = $strSysTempDir.DS.HTTP_HOST;
    // if (!is_dir($strSysTempDirApp)) {
    //     mkdir($strSysTempDirApp, 0770);
    //     chgrp($strSysTempDirApp, WWW_GRP);
    // }

    // Session
    $strSysTempDirAppSession = $strSysTempDirApp.DS.'Session'.DS;
    // if (!is_dir($strSysTempDirAppSession)) {
    //     mkdir($strSysTempDirAppSession, 0770);
    //     chgrp($strSysTempDirAppSession, WWW_GRP);
    // }
    define('SESSION_DIR', $strSysTempDirAppSession);

    // Cache phpFastCache
    $strSysTempDirAppFiles = $strSysTempDirApp.DS.'Files'.DS;
    // if (!is_dir($strSysTempDirAppFiles)) {
    //     mkdir($strSysTempDirAppFiles, 0770);
    //     chgrp($strSysTempDirAppFiles, WWW_GRP);
    // }
    define('CACHE_DB_PATH', $strSysTempDir); // Main temporary directory of the server, not the full path to the temporary directory of the application.

    // Cache Smarty
    $strSysTempDirAppCacheSmarty = $strSysTempDirApp.DS.'CacheSmarty'.DS;
    // if (!is_dir($strSysTempDirAppCacheSmarty)) {
    //     mkdir($strSysTempDirAppCacheSmarty, 0770);
    //     chgrp($strSysTempDirAppCacheSmarty, WWW_GRP);
    // }
    define('SMARTY_CACHE_DIR', $strSysTempDirAppCacheSmarty);

    // Compiler Smarty
    $strSysTempDirAppCompilerSmarty = $strSysTempDirApp.DS.'CompilerSmarty'.DS;
    // if (!is_dir($strSysTempDirAppCompilerSmarty)) {
    //     mkdir($strSysTempDirAppCompilerSmarty, 0770);
    //     chgrp($strSysTempDirAppCompilerSmarty, WWW_GRP);
    // }
    define('SMARTY_COMPILE_DIR', $strSysTempDirAppCompilerSmarty);

    // umask($old_umask);
}

function configApps(): bool
{
    define('APPS_SETTINGS_PATH', PARTICLE_PATH_APPS.'Settings'.DS);

    // Load configs Apps
    require_once APPS_SETTINGS_PATH.'global.inc.php';

    if (file_exists(APPS_SETTINGS_PATH.'database.inc.test.php')) {
        require_once APPS_SETTINGS_PATH.'database.inc.test.php'; // only for secret tests
    } else {
        require_once APPS_SETTINGS_PATH.'database.inc.php';
    }

    /* CACHE TPL AND OUTPUT BUFFER */
    define('CACHE_TPL', false);
    define('CACHE_TPL_TIME', 3600); // seconds
    define('CACHE_TPL_LIMIT_MB', 1);
    define('DEBUG_MODE', false);
    define('OUTPUT_CONTROL', false);

    /* CACHE DB FILE */
    define('CACHE_DB', true);
    define('CACHE_DB_TIME', 3600); // seconds

    /* Force HTTP_HOST config o check */
    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER['HTTP_HOST'] = HTTP_HOST;
    } elseif ($_SERVER['HTTP_HOST'] != HTTP_HOST) {
        return false;
    }

    /* Set Application temporary directory */
    $strSysTempDir = sys_get_temp_dir();
    prepareSysTempDir($strSysTempDir);

    return true;
}
