<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @name Request
 *
 *  @category Particle\Core
 *
 *  @author dertin
 *
 *  @todo Implementar cache para cuando llegue un request ya conocido.
 *
 *  Esta class se encarga de recibir el request del usuario
 *  y lo envia al metodo run de la class Bootstrap.
 **/
final class Request
{
    /**
     * request que solicito el usuario.
     *
     * @var string request
     */
    private $_request = '';
    /**
     * controller para la solicitud del usuario.
     *
     * @var string
     */
    private $_controller = null;
    /**
     * method para la solicitud del usuario.
     *
     * @var string
     */
    private $_method = null;
    /**
     * argumentos de la solicitud del usuario.
     *
     * @var array
     */
    private $_args = null;
    /**
     * array de urls definidas en el router xml.
     *
     * @see /Apps/router/default.xml
     *
     * @var array
     */
    private $_aURLs = array();
    /**
     * array de reglas definidas en el mapping xml
     * (las reglas se utilizan para filtrar los argumentos del request/url).
     *
     * @see /Apps/mapping/default.xml
     *
     * @var array
     */
    private $_aRules = array();
    /**
     * array con elementos para aplicar reglas y obtener los argumentos.
     * en caso de no existir toma el valor de FALSE.
     *
     * @var array
     */
    private $_aArgsToFilter = array();
    /**
     * flag para determinar si el request es del tipo mapping o comun patron MVC.
     *
     * @var bool
     */
    private $_flagMapping = false;

    public function __construct($requestTesting = null)
    {
        $this->_request = null;

        if (isset($_GET['request'])) {
          $this->_request = filter_input(INPUT_GET, 'request', FILTER_SANITIZE_URL);
        }elseif (!empty($requestTesting)) {
          $this->_request = $requestTesting;
        }

        if(!empty($this->_request)){
          $this->loadClassAttributes($this->xmlSetMapping());
        }

        if (!$this->_controller && DEFAULT_CONTROLLER) {
            $this->_controller = DEFAULT_CONTROLLER;
        }

        if (!$this->_method && !STRICT_ROUTING) {
            $this->_method = 'index';
        }

        if (!isset($this->_args)) {
            $this->_args = array();
        }
    }

    final private function xmlSetMapping()
    {
        $this->_flagMapping = true;

        if (is_readable(MAPPING_PATH)) {
            $simplexml_load_file = simplexml_load_file(MAPPING_PATH);

            if (!$simplexml_load_file) {
                throw new \Exception('Error XML Mapping');
            }

            $xmlData = json_decode(json_encode((array) $simplexml_load_file), 1);

            if ($xmlData) {
                return $xmlData;
            } else {
                $this->_flagMapping = false;
                throw new \Exception('Error File Mapping');
            }
        } else {
            $this->_flagMapping = false;
            throw new \Exception('Error File Mapping');
        }
    }

    final private function loadClassAttributes($aMapping = false)
    {
        if (!isset($this->_request) || !is_string($this->_request)) {
            throw new \Exception('Bad Request');

            return false;
        }

        if (MAPPING_ROUTING) {
            if (!isset($aMapping) || count($aMapping) <= 0 || !is_array($aMapping) || !$this->_flagMapping || !isset($aMapping['url']) || count($aMapping['url']) <= 0 || !is_array($aMapping['url'])) {
                $this->_flagMapping = false;
            } else {
                $this->_flagMapping = true;

                $this->_aURLs = $aMapping['url'];

                if (isset($this->_aURLs['@attributes']) && is_array($this->_aURLs['@attributes'])) {
                    $this->_aURLs[0] = $this->_aURLs;
                    unset($this->_aURLs['@attributes']);
                    unset($this->_aURLs['controller']);
                    unset($this->_aURLs['method']);
                    unset($this->_aURLs['argument']);
                }

                if (isset($aMapping['rule']) && is_array($aMapping['rule'])) {
                    $this->_aRules = $aMapping['rule'];

                    if (isset($this->_aRules['@attributes']) && is_array($this->_aRules['@attributes'])) {
                        $this->_aRules[0] = $this->_aRules;
                        unset($this->_aRules['@attributes']);
                        unset($this->_aRules['splitter']);
                        unset($this->_aRules['space']);
                        unset($this->_aRules['ignore']);
                        unset($this->_aRules['ignorepreg']);
                    }
                }

                for ($i = 0; $i < count($this->_aURLs); ++$i) {
                    $sWildCard = false;

                    if (!isset($this->_aURLs[$i]['@attributes']['request']) || !is_string($this->_aURLs[$i]['@attributes']['request']) || empty($this->_aURLs[$i]['@attributes']['request'])) {
                        throw new \Exception('Bad XML Request');
                    }
                    if (!isset($this->_aURLs[$i]['controller']) || !is_string($this->_aURLs[$i]['controller']) || empty($this->_aURLs[$i]['controller'])) {
                        throw new \Exception('Bad XML Controller');
                    }
                    if (!isset($this->_aURLs[$i]['method']) || !is_string($this->_aURLs[$i]['method']) || empty($this->_aURLs[$i]['method'])) {
                        throw new \Exception('Bad XML Method');
                    }

                    if (isset($this->_aURLs[$i]['argument'])) {
                        if (!is_array($this->_aURLs[$i]['argument'])) {
                            if (!is_string($this->_aURLs[$i]['argument'])) {
                                throw new \Exception('Bad XML Args');
                            }
                        }
                    } else {
                        $this->_aURLs[$i]['argument'] = null;
                    }

                    if (isset($this->_aURLs[$i]['@attributes']['rule']) && !empty($this->_aURLs[$i]['@attributes']['rule']) && isset($this->_aURLs[$i]['@attributes']['wildcard']) && !empty($this->_aURLs[$i]['@attributes']['wildcard'])) {
                        if (!is_string($this->_aURLs[$i]['@attributes']['rule'])) {
                            throw new \Exception('Bad XML Rule');

                            return false;
                        }

                        if (!is_string($this->_aURLs[$i]['@attributes']['wildcard'])) {
                            throw new \Exception('Bad XML WildCard');

                            return false;
                        }

                        $intCountRule = count(explode('|', $this->_aURLs[$i]['@attributes']['rule']));

                        $intCountWildCard = substr_count($this->_aURLs[$i]['@attributes']['request'], $this->_aURLs[$i]['@attributes']['wildcard']);

                        if ($intCountRule != $intCountWildCard) {
                            throw new \Exception('Bad XML Config Rule/WildCard');

                            return false;
                        }

                        $sWildCard = $this->_aURLs[$i]['@attributes']['wildcard'];
                    } else {
                        $sWildCard = false;
                    }

                    if ($this->compareRequestXMLURL($this->_aURLs[$i]['@attributes']['request'], $this->_request, $sWildCard)) {
                        $this->_flagMapping = true;

                        $this->_controller = $this->_aURLs[$i]['controller'];

                        $this->_method = $this->_aURLs[$i]['method'];

                        if (isset($this->_aURLs[$i]['argument']) && !is_null($this->_aURLs[$i]['argument'])) {
                            if (isset($this->_aURLs[$i]['argument']['value'])) {
                                if (isset($this->_aURLs[$i]['argument']['@attributes']['name']) && !empty($this->_aURLs[$i]['argument']['@attributes']['name'])) {
                                    $this->_args[$this->_aURLs[$i]['argument']['@attributes']['name']] = Core\Security::cleanHtml($this->_aURLs[$i]['argument']['value']);
                                } else {
                                    $this->_args[] = Core\Security::cleanHtml($this->_aURLs[$i]['argument']['value']);
                                }
                            }
                        }

                        if (is_string($sWildCard) && !empty($sWildCard) && is_string($this->_aURLs[$i]['@attributes']['rule']) && !empty($this->_aURLs[$i]['@attributes']['rule'])) {
                            $aRuleXML = explode('|', $this->_aURLs[$i]['@attributes']['rule']);

                            if (isset($this->_aURLs[$i]['@attributes']['argumentname']) && is_string($this->_aURLs[$i]['@attributes']['argumentname']) && !empty($this->_aURLs[$i]['@attributes']['argumentname'])) {
                                $aNameAgrsXML = explode('|', $this->_aURLs[$i]['@attributes']['argumentname']);

                                if (!isset($aNameAgrsXML) || is_null($aNameAgrsXML) || empty($aNameAgrsXML)) {
                                    $aNameAgrsXML = false;
                                } else {
                                    if (count($aRuleXML) != count($aNameAgrsXML)) {
                                        throw new \Exception('Bad XML Config Rules/NameArgs');

                                        return false;
                                    }
                                }
                            } else {
                                $aNameAgrsXML = false;
                            }

                            if (!$this->applyRule($aRuleXML, $aNameAgrsXML)) {
                                $this->_flagMapping = false;
                            }
                        }

                        break;
                    } else {
                        $this->_flagMapping = false;
                    }
                }
            }
        } else {
            $this->_flagMapping = false;
        }

        if (DEFAULT_ROUTING) {
            if (!$this->_flagMapping) {
                $requestTemp = $this->_request;

                $requestExplode = explode('/', $requestTemp);

                // Empty values are now allowed - TODO: Only allow numeric and string values equal to 0
                //$arrayRequest = array_filter($requestExplode);

                $arrayRequest = $requestExplode;

                $this->_controller = strtolower(array_shift($arrayRequest));
                $this->_method = strtolower(array_shift($arrayRequest));
                $this->_args = Core\Security::filterAlphaNum($arrayRequest);
            }
        }

        if ((!DEFAULT_ROUTING && !MAPPING_ROUTING)) {
            throw new \Exception('Bad Config ROUTING');
        } else {
            return true;
        }
    }

    final private function compareRequestXMLURL($requestXML, $requestURL, $sWildCard = false)
    {
        if (!is_string($requestXML) || !is_string($requestURL) || empty($requestXML) || empty($requestURL)) {
            throw new \Exception('Bad Value XML/URL Request');

            return false;
        } else {
            if (is_string($sWildCard) && !empty($sWildCard)) {
                $result = array();

                $sPregRequest = '/^';
                $requestTemp1 = str_replace($sWildCard, '(.+)', $requestXML);
                $requestTemp2 = str_replace('/', '\/', $requestTemp1);
                $sPregRequest .= $requestTemp2;
                $sPregRequest .= '/';

                preg_match_all($sPregRequest, $requestURL, $result, PREG_SET_ORDER);

                if (count($result) > 0) {
                    $this->_aArgsToFilter = $result[0];

                    unset($this->_aArgsToFilter[0]);

                    $this->_aArgsToFilter = array_values($this->_aArgsToFilter);

                    return true;
                } else {
                    return false;
                }
            } else {
                if ($requestXML == $requestURL) {
                    $this->_aArgsToFilter = false;

                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    final private function applyRule($aURLRuleXML, $aNameAgrsXML = false)
    {
        if (!$this->_aArgsToFilter) {
            return false;
        }

        $bRule = false;
        $aRuleArgs = array();
        $aRuleNameArgs = array();

        $countURLRuleXML = count($aURLRuleXML);
        $countArgsName = count($aNameAgrsXML);
        $countRules = count($this->_aRules);
        $countArgsToFilter = count($this->_aArgsToFilter);

        if ($countURLRuleXML != $countArgsToFilter) {
            throw new \Exception('Not applicable Rules/Wildcard ');
        }

        for ($u = 0; $u < $countURLRuleXML; ++$u) {
            $aRuleArgs[$u]['name'] = $aURLRuleXML[$u];
        }

        for ($a = 0; $a < $countArgsName; ++$a) {
            $aRuleNameArgs[$a]['key'] = $aNameAgrsXML[$a];
        }

        for ($s = 0; $s < $countArgsToFilter; ++$s) {
            $aRuleArgs[$s]['target'] = $this->_aArgsToFilter[$s];
        }

        $countRuleArgs = count($aRuleArgs);

        if ($countURLRuleXML != $countRuleArgs) {
            throw new \Exception('Not applicable Rules/Args ');
        }

        for ($a = 0; $a < $countRuleArgs; ++$a) {
            for ($i = 0; $i < $countRules; ++$i) {

                /*echo'aaa<pre>'; var_dump($aRuleArgs[$a]['name']);
                echo '</pre>bbb<pre>';
                var_dump($this->_aRules[$i]['@attributes']['name']);
                echo'</pre>';*/

                if (!isset($this->_aRules[$i]['@attributes']['name'])) {
                    continue;
                }
                if ($aRuleArgs[$a]['name'] == $this->_aRules[$i]['@attributes']['name'] && $aRuleArgs[$a]['name'] != 'notargument') {
                    $tempArg = $aRuleArgs[$a]['target'];

                    if (!empty($this->_aRules[$i]['ignore'])) {
                        $ignoreRule = $this->_aRules[$i]['ignore'];
                        $tempArg = str_replace($ignoreRule, '', $tempArg);
                    }

                    if (!empty($this->_aRules[$i]['ignorepreg'])) {
                        $ignorePregRule = $this->_aRules[$i]['ignorepreg'];
                        $tempArg = preg_replace($ignorePregRule, '', $tempArg);
                    }

                    $tempArg = trim($tempArg);

                    if (!empty($this->_aRules[$i]['space'])) {
                        $spaceRule = $this->_aRules[$i]['space'];
                        $tempArg = str_replace($spaceRule, ' ', $tempArg);
                    }

                    if (!empty($this->_aRules[$i]['splitter'])) {
                        $splitterRule = $this->_aRules[$i]['splitter'];
                        $tempArg = explode($splitterRule, $tempArg);
                    } else {
                        throw new \Exception('Splitter Rule is not defined');

                        return false;
                    }

                    if (is_null($this->_args)) {
                        $this->_args = array();
                    }

                    foreach ($tempArg as $value) {
                        if (isset($aRuleNameArgs[$a]['key'])) {
                            $this->_args[$aRuleNameArgs[$a]['key']] = $value;
                        } else {
                            array_push($this->_args, $value);
                        }
                    }

                    $bRule = true;
                }
            }
        }

        if (!$bRule) {
            throw new \Exception('Rule is not defined');

            return false;
        }

        return true;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function getControlador()
    {
        return $this->_controller;
    }

    public function getMetodo()
    {
        return $this->_method;
    }

    public function getArgs()
    {
        return $this->_args;
    }
}
