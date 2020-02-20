<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @category Particle\Core
 *  @author dertin
 **/
final class Crontab
{
    public static function run()
    {
        Core\Security::onlyCliOrExit();

        if (SESSION_GC == 'crontab') {
            $session = Core\Session::singleton();
            $session->purge();
            echo "run.purge";
        }
    }
}
// crontab -l | { cat; echo "15 * * * * /usr/local/php7/bin/php /var/www/webdisk/site.com/htdocs/Particle/Cli/crontab.php >/dev/null 2>&1"; } | crontab -
