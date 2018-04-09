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
    private $request = '';
    /**
     * controller para la solicitud del usuario.
     *
     * @var string
     */
    private $controller = null;
    /**
     * method para la solicitud del usuario.
     *
     * @var string
     */
    private $method = null;
    /**
     * argumentos de la solicitud del usuario.
     *
     * @var array
     */
    private $args = null;
    /**
     * array de urls definidas en el router xml.
     *
     * @see /Apps/router/default.xml
     *
     * @var array
     */
    private $aURLs = array();
    /**
     * array de reglas definidas en el mapping xml
     * (las reglas se utilizan para filtrar los argumentos del request/url).
     *
     * @see /Apps/mapping/default.xml
     *
     * @var array
     */
    private $aRules = array();
    /**
     * array con elementos para aplicar reglas y obtener los argumentos.
     * en caso de no existir toma el valor de FALSE.
     *
     * @var array
     */
    private $aArgsToFilter = array();
    /**
     * flag para determinar si el request es del tipo mapping o comun patron MVC.
     *
     * @var bool
     */
    private $flagMapping = false;

    public function __construct($requestTesting = null)
    {
        $this->request = null;

        if (isset($_GET['request'])) {
            $this->request = filter_input(INPUT_GET, 'request', FILTER_SANITIZE_URL);
        } elseif (!empty($requestTesting)) {
            $this->request = $requestTesting;
        }

        if (!empty($this->request)) {
            $this->loadClassAttributes($this->xmlSetMapping());
        }

        if (!$this->controller && DEFAULT_CONTROLLER) {
            $this->controller = DEFAULT_CONTROLLER;
        }

        if (!$this->method && !STRICT_ROUTING) {
            $this->method = 'index';
        }

        if (!isset($this->args)) {
            $this->args = array();
        }
    }

    final private function xmlSetMapping()
    {
        $this->flagMapping = true;

        if (is_readable(MAPPING_PATH)) {
            $simplexml_load_file = simplexml_load_file(MAPPING_PATH);

            if (!$simplexml_load_file) {
                throw new \Exception('Error XML Mapping');
            }

            $xmlData = json_decode(json_encode((array) $simplexml_load_file), 1);

            if ($xmlData) {
                return $xmlData;
            } else {
                $this->flagMapping = false;
                throw new \Exception('Error File Mapping');
            }
        } else {
            $this->flagMapping = false;
            throw new \Exception('Error File Mapping');
        }
    }

    final private function loadClassAttributes($aMapping = false)
    {
        if (!isset($this->request) || !is_string($this->request)) {
            throw new \Exception('Bad Request');

            return false;
        }

        if (MAPPING_ROUTING) {
            if (!isset($aMapping) || count($aMapping) <= 0 || !is_array($aMapping) || !$this->flagMapping || !isset($aMapping['url']) || count($aMapping['url']) <= 0 || !is_array($aMapping['url'])) {
                $this->flagMapping = false;
            } else {
                $this->flagMapping = true;

                $this->aURLs = $aMapping['url'];

                if (isset($this->aURLs['@attributes']) && is_array($this->aURLs['@attributes'])) {
                    $this->aURLs[0] = $this->aURLs;
                    unset($this->aURLs['@attributes']);
                    unset($this->aURLs['controller']);
                    unset($this->aURLs['method']);
                    unset($this->aURLs['argument']);
                }

                if (isset($aMapping['rule']) && is_array($aMapping['rule'])) {
                    $this->aRules = $aMapping['rule'];

                    if (isset($this->aRules['@attributes']) && is_array($this->aRules['@attributes'])) {
                        $this->aRules[0] = $this->aRules;
                        unset($this->aRules['@attributes']);
                        unset($this->aRules['splitter']);
                        unset($this->aRules['space']);
                        unset($this->aRules['ignore']);
                        unset($this->aRules['ignorepreg']);
                    }
                }

                for ($i = 0; $i < count($this->aURLs); ++$i) {
                    $sWildCard = false;

                    if (!isset($this->aURLs[$i]['@attributes']['request']) || !is_string($this->aURLs[$i]['@attributes']['request']) || empty($this->aURLs[$i]['@attributes']['request'])) {
                        throw new \Exception('Bad XML Request');
                    }
                    if (!isset($this->aURLs[$i]['controller']) || !is_string($this->aURLs[$i]['controller']) || empty($this->aURLs[$i]['controller'])) {
                        throw new \Exception('Bad XML Controller');
                    }
                    if (!isset($this->aURLs[$i]['method']) || !is_string($this->aURLs[$i]['method']) || empty($this->aURLs[$i]['method'])) {
                        throw new \Exception('Bad XML Method');
                    }

                    if (isset($this->aURLs[$i]['argument'])) {
                        if (!is_array($this->aURLs[$i]['argument'])) {
                            if (!is_string($this->aURLs[$i]['argument'])) {
                                throw new \Exception('Bad XML Args');
                            }
                        }
                    } else {
                        $this->aURLs[$i]['argument'] = null;
                    }

                    if (isset($this->aURLs[$i]['@attributes']['rule']) && !empty($this->aURLs[$i]['@attributes']['rule']) && isset($this->aURLs[$i]['@attributes']['wildcard']) && !empty($this->aURLs[$i]['@attributes']['wildcard'])) {
                        if (!is_string($this->aURLs[$i]['@attributes']['rule'])) {
                            throw new \Exception('Bad XML Rule');

                            return false;
                        }

                        if (!is_string($this->aURLs[$i]['@attributes']['wildcard'])) {
                            throw new \Exception('Bad XML WildCard');

                            return false;
                        }

                        $intCountRule = count(explode('|', $this->aURLs[$i]['@attributes']['rule']));

                        $intCountWildCard = substr_count($this->aURLs[$i]['@attributes']['request'], $this->aURLs[$i]['@attributes']['wildcard']);

                        if ($intCountRule != $intCountWildCard) {
                            throw new \Exception('Bad XML Config Rule/WildCard');

                            return false;
                        }

                        $sWildCard = $this->aURLs[$i]['@attributes']['wildcard'];
                    } else {
                        $sWildCard = false;
                    }

                    if ($this->compareRequestXMLURL($this->aURLs[$i]['@attributes']['request'], $this->request, $sWildCard)) {
                        $this->flagMapping = true;

                        $this->controller = $this->aURLs[$i]['controller'];

                        $this->method = $this->aURLs[$i]['method'];

                        if (isset($this->aURLs[$i]['argument']) && !is_null($this->aURLs[$i]['argument'])) {
                            if (isset($this->aURLs[$i]['argument']['value'])) {
                                if (isset($this->aURLs[$i]['argument']['@attributes']['name']) && !empty($this->aURLs[$i]['argument']['@attributes']['name'])) {
                                    $this->args[$this->aURLs[$i]['argument']['@attributes']['name']] = Core\Security::cleanHtml($this->aURLs[$i]['argument']['value']);
                                } else {
                                    $this->args[] = Core\Security::cleanHtml($this->aURLs[$i]['argument']['value']);
                                }
                            }
                        }

                        if (is_string($sWildCard) && !empty($sWildCard) && is_string($this->aURLs[$i]['@attributes']['rule']) && !empty($this->aURLs[$i]['@attributes']['rule'])) {
                            $aRuleXML = explode('|', $this->aURLs[$i]['@attributes']['rule']);

                            if (isset($this->aURLs[$i]['@attributes']['argumentname']) && is_string($this->aURLs[$i]['@attributes']['argumentname']) && !empty($this->aURLs[$i]['@attributes']['argumentname'])) {
                                $aNameAgrsXML = explode('|', $this->aURLs[$i]['@attributes']['argumentname']);

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
                                $this->flagMapping = false;
                            }
                        }

                        break;
                    } else {
                        $this->flagMapping = false;
                    }
                }
            }
        } else {
            $this->flagMapping = false;
        }

        if (DEFAULT_ROUTING) {
            if (!$this->flagMapping) {
                $requestTemp = $this->request;

                $requestExplode = explode('/', $requestTemp);

                // Empty values are now allowed - TODO: Only allow numeric and string values equal to 0
                //$arrayRequest = array_filter($requestExplode);

                $arrayRequest = $requestExplode;

                $this->controller = strtolower(array_shift($arrayRequest));
                $this->method = strtolower(array_shift($arrayRequest));
                $this->args = Core\Security::filterAlphaNum($arrayRequest);
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
                    $this->aArgsToFilter = $result[0];

                    unset($this->aArgsToFilter[0]);

                    $this->aArgsToFilter = array_values($this->aArgsToFilter);

                    return true;
                } else {
                    return false;
                }
            } else {
                if ($requestXML == $requestURL) {
                    $this->aArgsToFilter = false;

                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    final private function applyRule($aURLRuleXML, $aNameAgrsXML = false)
    {
        if (!$this->aArgsToFilter) {
            return false;
        }

        $bRule = false;
        $aRuleArgs = array();
        $aRuleNameArgs = array();

        $countURLRuleXML = count($aURLRuleXML);
        $countArgsName = count($aNameAgrsXML);
        $countRules = count($this->aRules);
        $countArgsToFilter = count($this->aArgsToFilter);

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
            $aRuleArgs[$s]['target'] = $this->aArgsToFilter[$s];
        }

        $countRuleArgs = count($aRuleArgs);

        if ($countURLRuleXML != $countRuleArgs) {
            throw new \Exception('Not applicable Rules/Args ');
        }

        for ($a = 0; $a < $countRuleArgs; ++$a) {
            for ($i = 0; $i < $countRules; ++$i) {
                if (!isset($this->aRules[$i]['@attributes']['name'])) {
                    continue;
                }
                if ($aRuleArgs[$a]['name'] == $this->aRules[$i]['@attributes']['name'] && $aRuleArgs[$a]['name'] != 'notargument') {
                    $tempArg = $aRuleArgs[$a]['target'];

                    if (!empty($this->aRules[$i]['ignore'])) {
                        $ignoreRule = $this->aRules[$i]['ignore'];
                        $tempArg = str_replace($ignoreRule, '', $tempArg);
                    }

                    if (!empty($this->aRules[$i]['ignorepreg'])) {
                        $ignorePregRule = $this->aRules[$i]['ignorepreg'];
                        $tempArg = preg_replace($ignorePregRule, '', $tempArg);
                    }

                    $tempArg = trim($tempArg);

                    if (!empty($this->aRules[$i]['space'])) {
                        $spaceRule = $this->aRules[$i]['space'];
                        $tempArg = str_replace($spaceRule, ' ', $tempArg);
                    }

                    if (!empty($this->aRules[$i]['splitter'])) {
                        $splitterRule = $this->aRules[$i]['splitter'];
                        $tempArg = explode($splitterRule, $tempArg);
                    } else {
                        throw new \Exception('Splitter Rule is not defined');

                        return false;
                    }

                    if (is_null($this->args)) {
                        $this->args = array();
                    }

                    foreach ($tempArg as $value) {
                        if (isset($aRuleNameArgs[$a]['key'])) {
                            $this->args[$aRuleNameArgs[$a]['key']] = $value;
                        } else {
                            array_push($this->args, $value);
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
        return $this->request;
    }

    public function getControlador()
    {
        return $this->controller;
    }

    public function getMetodo()
    {
        return $this->method;
    }

    public function getArgs()
    {
        return $this->args;
    }
}
