<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @name Cookies
 *
 *  @category Particle\Core
 *
 *  @author dertin
 */
final class Session extends \SessionHandler
{
    protected $name;
    protected $cookie;
    private static $instancia;

    public static function singleton()
    {
        if (!isset(self::$instancia)) {
            $miclase = __CLASS__;
            self::$instancia = new $miclase(SESSION_NAME);
            session_set_save_handler(self::$instancia, true);
        }

        self::$instancia->start();

        return self::$instancia;
    }

    public function __construct($name)
    {
        $this->name = $name;
        // Consigue los parámetros de la cookie de la sesión
        $this->cookie = session_get_cookie_params();
        // register_shutdown_function('session_write_close');
        $this->setup();
    }

    public function __set(string $name, string $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function __get(string $name): ?string
    {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return null;
    }

    private function setup(): void
    {
        ini_set('session.save_handler', 'files');

        $session_hash = 'sha512';

        if (in_array($session_hash, hash_algos())) {
            ini_set('session.hash_function', $session_hash);
        }
        ini_set('session.hash_bits_per_character', 5);

        ini_set('session.use_only_cookies', 1);

        if (SESSION_GC == 'crontab') {
            ini_set('session.gc_probability', 0); // disabled
        } else {
            ini_set('session.gc_probability', 1);
        }

        $httponly = true;
        $secure = isset($_SERVER['HTTPS']);

        session_set_cookie_params($this->cookie["lifetime"], $this->cookie["path"], $this->cookie["domain"], $secure, $httponly);

        session_name($this->name);

        $sessionPath = SYS_TEMP_DIR;
        session_save_path($sessionPath);
    }

    private function refresh(): bool
    {
        // Create new session without destroying the old one
        session_regenerate_id(false);

        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();

        // Set session ID to the new one, and start it back up again
        session_id($newSession);
        session_start();

        return true;
    }

    private function start(): bool
    {
        if (session_id() === '') {
            session_start();
        }

        if (!$this->isValid()) {
            $this->forget();
            $isValid = 'valida no';
        } else {
            if (mt_rand(0, 4) === 0) {
                $this->refresh();
            }
        }

        if (session_id() === '') {
            session_start();
        }

        $_SESSION['_last_activity'] = time();

        return true;
    }

    private function forget(): bool
    {
        if (session_id() === '') {
            return true;
        }
        $_SESSION = [];
        setcookie(
            $this->name,
            '',
            time() - 42000,
            $this->cookie['path'],
            $this->cookie['domain'],
            $this->cookie['secure'],
            $this->cookie['httponly']
        );
        return session_destroy();
    }

    private function isExpired(int $ttl = 0): bool
    {
        if (empty($ttl)) {
            $ttl = SESSION_TIME;
        }

        $last = isset($_SESSION['_last_activity'])
            ? $_SESSION['_last_activity']
            : false;

        if ($last !== false && time() - $last > $ttl) {
            return true;
        }

        $_SESSION['_last_activity'] = time();

        return false;
    }

    private function isFingerprint(): bool
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']): '';
        $userIP = isset($_SERVER['REMOTE_ADDR']) ? strtolower($_SERVER['REMOTE_ADDR']): '127.0.0.1';

        $hash = md5(
            $userAgent .
            (ip2long($userIP) & ip2long('255.255.0.0'))
        );
        if (isset($_SESSION['_fingerprint'])) {
            return $_SESSION['_fingerprint'] === $hash;
        }
        $_SESSION['_fingerprint'] = $hash;
        return true;
    }

    private function isValid(): bool
    {
        return ! $this->isExpired() && $this->isFingerprint();
    }

    public function read($id): string
    {
        $strSessionValue = parent::read($id);

        if (isset($strSessionValue) && !empty($strSessionValue)) {
            $valueDecrypt = Core\Security::decrypt((string)$strSessionValue, SESSION_KEY, SALT_CODE);
            return $valueDecrypt;
        }
        return '';
    }

    public function write($id, $value = ''): bool
    {
        $valueEncrypt = Core\Security::encrypt((string)$value, SESSION_KEY, SALT_CODE);
        return parent::write($id, $valueEncrypt);
    }

    public function purge(): bool
    {
        // if (empty($maxlifetime)) {
        //     $maxlifetime = SESSION_GC_TIME;
        // }
        //parent::gc($maxlifetime);
        session_gc();
        $this->forget();
        return true;
    }
}
