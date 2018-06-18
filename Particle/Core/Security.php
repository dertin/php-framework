<?php

namespace Particle\Core;

final class Security
{
    final private static function arrayMapRecursive($fn, $arr, $recursive = false)
    {
        $rarr = array();

        foreach ($arr as $k => $v) {
            $rarr[$k] = is_array($v) ? self::arrayMapRecursive($fn, $v, true) : call_user_func($fn, $v);
        }

        if ($recursive) {
            return $rarr;
        } else {
            return $rarr[0];
        }
    }

    final public static function isValidIdSQL($id)
    {
        if (isset($id) && !empty($id) && is_numeric($id) && $id >= 0) {
            return true; // Valid Id
        }
        return false;
    }

    final public static function isValidValueSQL($value)
    {
        if (isset($value) && !empty($value) && is_string($value)) {
            return true; // Valid value
        }
        return false;
    }

    final public static function filterInt($int, $default = 0)
    {
        $int = (int) $int;

        if (is_int($int)) {
            return $int;
        } else {
            return $default;
        }
    }

    final public static function filterAlphaNum($value, $default = '')
    {
        if (is_array($value)) {
            $filterStr = self::arrayMapRecursive(array('Particle\Core\Security', 'filterAlphaNum'), array($value, $default));
            return $filterStr;
        } elseif (is_string($value)) {
            $filterStr = (string) preg_replace('/[^A-Z0-9_-]/i', '', $value);
            return trim($filterStr);
        }

        return $default;
    }

    final public static function htmlescape($strHtml, $default = '', $removeHtml = false, $allowable_tags = null)
    {
        if (is_string($strHtml)) {
            if ($removeHtml) {
                $filterStrHtml = strip_tags($strHtml, $allowable_tags);
            } else {
                $filterStrHtml = htmlspecialchars($strHtml, ENT_QUOTES, CHARSET);
            }

            return $filterStrHtml;
        } else {
            return $default;
        }
    }

    final public static function cleanHtml($filterHtml, $default = '', $removeHtml = false, $allowable_tags = null)
    {
        if (is_array($filterHtml)) {
            $filterHtml = self::arrayMapRecursive(array('Particle\Core\Security', 'htmlescape'), array($filterHtml, $default, $removeHtml, $allowable_tags));
        } elseif (is_string($filterHtml)) {
            $filterHtml = self::htmlescape($filterHtml, $default, $removeHtml, $allowable_tags);
        } else {
            return $default;
        }

        return $filterHtml;
    }

    final public static function filterSql($sql, $html = true, $default = false)
    {
        if (isset($sql) && !empty($sql) && is_string($sql)) {
            if ($html) {
                if (DOCTYPE == 'HTML5') {
                    $iDocType = ENT_HTML5;
                } elseif (DOCTYPE == 'XHTML') {
                    $iDocType = ENT_XHTML;
                } else {
                    $iDocType = ENT_HTML401;
                }

                $sql = htmlspecialchars($sql, ENT_NOQUOTES | $iDocType, CHARSET);
            }

            if (!get_magic_quotes_gpc()) {
                $sql = addslashes($sql);
            }

            return trim($sql);
        } else {
            return $default;
        }
    }

    final public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    final public static function checkDateRange($start_date, $end_date, $evaluame)
    {
        // data format YYYY-mm-dd
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($evaluame);
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }


    final public static function validateEmail($email)
    {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    final public static function validateCedula($CedulaDeIdentidad)
    {
        $regexCI = '/^([0-9]{1}[.]?[0-9]{3}[.]?[0-9]{3}[-]?[0-9]{1}|[0-9]{3}[.]?[0-9]{3}[-]?[0-9]{1})$/';
        if (!preg_match($regexCI, $CedulaDeIdentidad)) {
            return false;
        } else {
            // Limpiamos los puntos y guiones para solo quedarnos con los números.
            $numeroCedulaDeIdentidad = preg_replace("/[^0-9]/", "", $CedulaDeIdentidad);
            // Armarmos el array que va a permitir realizar las multiplicaciones necesarias en cada digito.
            $arrayCoeficiente = [2,9,8,7,6,3,4,1];
            // Variable donde se va a guardar el resultado de la suma.
            $suma = 0;
            // Simplemente para que se entienda que esto es el cardinal de digitos que tiene el array de coeficiente.
            $lenghtArrayCoeficiente = 8;
            // Contamos la cantidad de digitos que tiene la cadena de números de la CI que limpiamos.
            $lenghtCedulaDeIdentidad = strlen($numeroCedulaDeIdentidad);
            // Esto nos asegura que si la cédula es menor a un millón, para que el cálculo siga funcionando, simplemente le ponemos un cero antes y funciona perfecto.
            if ($lenghtCedulaDeIdentidad == 7) {
                $numeroCedulaDeIdentidad = 0 . $numeroCedulaDeIdentidad;
                $lenghtCedulaDeIdentidad++;
            }
            for ($i = 0; $i < $lenghtCedulaDeIdentidad; $i++) {
                // Voy obteniendo cada caracter de la CI.
                $digito = substr($numeroCedulaDeIdentidad, $i, 1);
                // Ahora lo forzamos a ser un int.
                $digitoINT = intval($digito);
                // Obtengo el coeficiente correspondiente a esta posición.
                $coeficiente = $arrayCoeficiente[$i];
                // Multiplico el caracter por el coeficiente y lo acumulo a la suma total
                $suma = $suma + $digitoINT * $coeficiente;
            }
            // si la suma es múltiplo de 10 es una ci válida
            if (($suma % 10) == 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    final public static function filterXSStext($valXSS, $default = '')
    {
        if (!isset($valXSS) || empty($valXSS) || !is_string($valXSS)) {
            return $default;
        }

        $valTrim = trim($valXSS);

        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $valTrim);

        $search = 'abcdefghijklmnopqrstuvwxyzÃ¡Ã©Ã­Ã³ÃºÃ±';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZÃÃÃÃÃÃ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';

        for ($i = 0; $i < strlen($search); ++$i) {
            $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val);
        }

        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        $found = true;

        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); ++$i) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); ++$j) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
                $val = preg_replace($pattern, $replacement, $val);
                if ($val_before == $val) {
                    $found = false;
                }
            }
        }

        $valNoXSS = strip_tags($val);

        return $valNoXSS;
    }

    final public static function filterXSS($valXSS, $default = '')
    {
        if (is_array($valXSS)) {
            $valXSS = self::arrayMapRecursive(array('Particle\Core\Security', 'filterXSStext'), array($valXSS, $default));
        } elseif (is_string($valXSS)) {
            $valXSS = self::filterXSStext($valXSS, $default);
        } else {
            return $default;
        }

        return $valXSS;
    }

    final public static function isSerialized($data, $strict = true)
    {
        // if it isn't a string, it isn't serialized.
        if (! is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }
        $token = $data[0];
        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                       return false;
                }
                // como no hay break; ingresa en los otros case
                // no break
            case 'a':
            case 'O':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }
        return false;
    }

    final public static function encrypt($decrypted = "", $password = '', $salt = SALT_CODE)
    {
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('sha256', $salt . $password);

        $ivSize = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivSize);

        // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
        $encrypted = openssl_encrypt($decrypted . md5($decrypted), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }

    final public static function decrypt($encrypted = null, $password = '', $salt = SALT_CODE)
    {
        if (empty($encrypted)) {
            return null;
        }
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('sha256', $salt . $password);

        $encrypted = base64_decode($encrypted);
        $ivSize = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($encrypted, 0, $ivSize);
        $decrypted = openssl_decrypt(substr($encrypted, $ivSize), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        // Retrieve $hash which is the last 32 characters of $decrypted.
        $verifyMD5 = substr($decrypted, -32);
        // Remove the last 32 characters from $decrypted.
        $decrypted = substr($decrypted, 0, -32);
        // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
        if (md5($decrypted) != $verifyMD5) {
            Core\Debug::savelogfile(0, 'ERROR', 'decrypt() invalid verify MD5');
            return false;
        }
        return $decrypted;
    }

    final public static function getIp()
    {
        /*$requestIP = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $requestIP = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $requestIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $requestIP = $_SERVER['REMOTE_ADDR'];
        }

        return $requestIP;*/

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            $client_ip =
             (!empty($_SERVER['REMOTE_ADDR'])) ?
                $_SERVER['REMOTE_ADDR']
                :
                ((!empty($_ENV['REMOTE_ADDR'])) ?
                   $_ENV['REMOTE_ADDR']
                   :
                   "unknown");

            // los proxys van añadiendo al final de esta cabecera
            // las direcciones ip que van "ocultando". Para localizar la ip real
            // del usuario se comienza a mirar por el principio hasta encontrar
            // una dirección ip que no sea del rango privado. En caso de no
            // encontrarse ninguna se toma como valor el REMOTE_ADDR

            $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);

            reset($entries);
            while (list(, $entry) = each($entries)) {
                $entry = trim($entry);
                if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                    // http://www.faqs.org/rfcs/rfc1918.html
                    $private_ip = array(
                      '/^0\./',
                      '/^127\.0\.0\.1/',
                      '/^192\.168\..*/',
                      '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                      '/^10\..*/');

                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                    if ($client_ip != $found_ip) {
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        } else {
            $client_ip =
             (!empty($_SERVER['REMOTE_ADDR'])) ?
                $_SERVER['REMOTE_ADDR']
                :
                ((!empty($_ENV['REMOTE_ADDR'])) ?
                   $_ENV['REMOTE_ADDR']
                   :
                   "unknown");
        }

        return $client_ip;
    }

    final public static function getIPNoFORWARDED()
    {
        /* Obtengo IP desde REMOTE_ADDR no importa si es la del proxy,
        no obtengo desde HTTP_X_FORWARDED_FOR evitando proxy ruidosos o cabezera PHP */
        $client_ip =(!empty($_SERVER['REMOTE_ADDR'])) ?$_SERVER['REMOTE_ADDR']:((!empty($_ENV['REMOTE_ADDR'])) ?$_ENV['REMOTE_ADDR']:"unknown");
        return $client_ip;
    }

    final public static function isValidArrayIsNumeric($aInput)
    {
        if (empty($aInput)) {
            return false;
        }
        foreach ($aInput as $key => $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }
        return true;
    }

    final public static function isValidJSON($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    final public static function getIntentoBruteForce($nameZone)
    {
        $intento = 0;

        $client_ip = self::getIPNoFORWARDED();

        $filename = $_SERVER['DOCUMENT_ROOT'].'/preventBF-'.$nameZone.'.log';

        $file = @fopen($filename, 'r');
        if (!$file) {
            // prevent loop infinito para feof
            $file = null;
            return 0;
        }
        while (!feof($file)) {
            $lineBF = fgets($file);
            if (!$lineBF) {
                continue;
            }
            $aLineBF = explode('|', $lineBF);

            if (is_array($aLineBF) && isset($aLineBF[1]) && isset($aLineBF[2])) {
                if ($client_ip == trim($aLineBF[1])) {
                    if (is_numeric(trim($aLineBF[2]))) {
                        $intento = (int)$aLineBF[2];
                    }
                }
            }
        }
        fclose($file);

        return $intento;
    }

    final public static function preventBruteForce($intento = 0, $nameZone = '')
    {
        if (empty($intento)) {
            return 0;
        }

        $client_ip = self::getIPNoFORWARDED();
        $filename = $_SERVER['DOCUMENT_ROOT'].'/preventBF-'.$nameZone.'.log';
        $file = @fopen($filename, 'a');
        if (!$file) {
            $file = null;
            return 0;
        }
        if ($file && !empty($client_ip)) {
            $sDataLog = date('Y-m-d H:i:s').'|'.$client_ip.'|'.$intento;
            fwrite($file, trim($sDataLog).PHP_EOL);
        }
        fclose($file);
    }

    final public static function resetBruteForce($nameZone)
    {
        $client_ip = self::getIPNoFORWARDED();

        $filename = $_SERVER['DOCUMENT_ROOT'].'/preventBF-'.$nameZone.'.log';

        $file = @fopen($filename, 'r');
        if (!$file) {
            // prevent loop infinito para feof
            $file = null;
            return 0;
        }
        $textNewFile =  '';
        while (!feof($file)) {
            $lineBF = fgets($file);
            if (!$lineBF) {
                continue;
            }
            $aLineBF = explode('|', $lineBF);
            if (is_array($aLineBF) && isset($aLineBF[1]) && isset($aLineBF[2])) {
                if ($client_ip != trim($aLineBF[1])) {
                    $textNewFile .= trim($lineBF).PHP_EOL;
                }
            }
        }
        fclose($file);
        // creo nuevo file
        $fileNew = @fopen($filename, 'w');
        if (!$file) {
            // prevent loop infinito para feof
            $file = null;
            return 0;
        }
        $rSaveNewFile = fwrite($fileNew, $textNewFile);
        fclose($fileNew);

        return $rSaveNewFile;
    }
}
