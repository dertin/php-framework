<?php

namespace Particle\Core;

use Particle\Core;

/**
 *  @name View
 *
 *  @category Particle\Core
 *
 *  @author dertin
 **/

final class View extends \Smarty
{
    private static $controller; // or name Addons
    private static $method;
    private static $cacheKey;

    private static $css;
    private static $js;
    private static $jsHead;

    private static $cssLayout;
    private static $jsLayout;
    private static $jsHeadLayout;
    private static $sCurrAssetName;

    private static $extraTplJS;
    private static $extraTplJSTop;

    private static $isAddons;

    public function __construct($controller, $method, $cacheKey = false, $isAddons = false)
    {
        parent::__construct();

        AssetConfig::getInstance()->init();

        $cacheDir = PARTICLE_PATH_CORE.'tmp'.DS.'cache-smarty'.DS;
        $compileDir = PARTICLE_PATH_CORE.'tmp'.DS.'template-compiler-smarty'.DS;
        $configDir = PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.'layout'.DS.DEFAULT_LAYOUT.DS.'configs-smarty'.DS.NAMEHOST.DS;

        $this->setCompileDir($compileDir);
        $this->setConfigDir($configDir);

        if (DEBUG_MODE) {
            $this->error_reporting = E_ALL;
            $this->debugging = true;
        } else {
            $this->debugging = false;
        }

        if (CACHE_TPL) {
            $this->caching = true;

            $this->setCacheDir($cacheDir);

            if (CACHE_TPL_TIME) {
                $this->clearAllCache(CACHE_TPL_TIME);
            }

            if (CACHE_TPL_LIMIT_MB) {
                if ($this->foldersize($cacheDir) >= CACHE_TPL_LIMIT_MB) {
                    $this->clearAllCache();
                }
            }
        } else {
            $this->caching = false;
        }

        self::$controller = $controller;
        self::$method = $method;
        self::$cacheKey = $cacheKey;
        self::$isAddons = $isAddons;

        self::$css = array();
        self::$js = array();
        self::$jsHead = array();

        self::$cssLayout = array();
        self::$jsLayout = array();
        self::$jsHeadLayout = array();
        self::$sCurrAssetName = 'default';
    }

    public function show($view = false, $customController = false, $return = false, $typereturn = false, $customLayout = false)
    {
        if (!isset($view) || !is_string($view)) {
            if (self::$isAddons) {
                // para Addons se debe indicar el nombre de view a mostrar siempre
                throw new \Exception('Error View Addons');
            } else {
                $view = self::$method;
            }
        }

        if (is_string($customController)) {
            $viewController = $customController;
        } else {
            $viewController = self::$controller;
        }

        $aCSS = array();
        $aJS = array();
        $aJSHead = array();

        $aCSSLayout = array();
        $aJSLayout = array();
        $aJSHeadLayout = array();

        $extraTplJS = '';
        $extraTplJSTop = '';

        if (count(self::$css)) {
            $aCSS = self::$css;
        }

        if (count(self::$js)) {
            $aJS = self::$js;
        }

        if (count(self::$jsHead)) {
            $aJSHead = self::$jsHead;
        }

        if (count(self::$cssLayout)) {
            $aCSSLayout = self::$cssLayout;
        }

        if (count(self::$jsLayout)) {
            $aJSLayout = self::$jsLayout;
        }

        if (count(self::$jsHeadLayout)) {
            $aJSHeadLayout = self::$jsHeadLayout;
        }

        if (!empty(self::$extraTplJS)) {
            $extraTplJS = self::$extraTplJS;
        }

        if (!empty(self::$extraTplJSTop)) {
            $extraTplJSTop = self::$extraTplJSTop;
        }

        $path_view = BASE_URL_APPS.VIEWS_FOLDER.'/'.$viewController;
        $path_static_view = BASE_URL_APPS_STATIC.VIEWS_FOLDER.'/'.$viewController;
        $absoluteView = PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.$viewController;

        // Change for Addons
        if (self::$isAddons) {
            $path_view = BASE_URL_APPS.ADDONS_FOLDER.'/'.$viewController.'/'.VIEWS_FOLDER.'/';
            $path_static_view = BASE_URL_APPS_STATIC.ADDONS_FOLDER.'/'.$viewController.'/'.VIEWS_FOLDER.'/';
            $absoluteView = ADDONS_PATH.$viewController.DS.VIEWS_FOLDER;
        }

        $pathView = $absoluteView.DS.$view.'.tpl';

        if (!empty($customLayout)) {
            // custom layout
            $pathLayoutURL = BASE_URL_APPS.VIEWS_FOLDER.'/layout/'.$customLayout;
            $pathStaticLayoutURL = BASE_URL_APPS_STATIC.VIEWS_FOLDER.'/layout/'.$customLayout;
            $pathLayoutFile = PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.'layout'.DS.$customLayout.DS;
        } else {
            // layout frontend
            $pathLayoutURL = BASE_URL_APPS.VIEWS_FOLDER.'/layout/'.DEFAULT_LAYOUT;
            $pathStaticLayoutURL = BASE_URL_APPS_STATIC.VIEWS_FOLDER.'/layout/'.DEFAULT_LAYOUT;
            $pathLayoutFile = PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.'layout'.DS.DEFAULT_LAYOUT.DS;
        }

        if (!is_dir($pathLayoutFile)) {
            Core\Debug::savelogfile(2, 'Error View Layout', 'The layout directory was not found');
            throw new \Exception('Error: The layout directory was not found');
        }

        $_params = array(
            'path_layout' => $pathLayoutURL,
            'path_static_layout' => $pathStaticLayoutURL,
            'css' => $aCSS,
            'js' => $aJS,
            'jsHead' => $aJSHead,
            'cssLayout' => $aCSSLayout,
            'jsLayout' => $aJSLayout,
            'jsHeadLayout' => $aJSHeadLayout,
            'extraTplJS' => $extraTplJS,
            'extraTplJSTop' => $extraTplJSTop,
            'home' => HOME_URL,
            'home_static' => HOME_URL_STATIC,
            'public' => BASE_URL_APPS.'public/',
            'public_static' => BASE_URL_APPS_STATIC.'public/',
            'controller' => $viewController,
            'tplname' => $view,
            'path_view' => $path_view,
            'path_static_view' => $path_static_view,
            'absolute_view' => $absoluteView,
            'url_controller' => HOME_URL.$viewController,
        );

        if (is_readable($pathView)) {
            $this->setTemplateDir($pathLayoutFile);
            $this->addTemplateDir($absoluteView, 'currentview');

            if (!$this->isCached('header.tpl', self::$cacheKey) || !$this->isCached('footer.tpl', self::$cacheKey)) {
                $this->assign('_layoutParams', $_params);
            }

            if (defined('VARGLOBALJS')) {
                $this->assign('varGlobalToJs', VARGLOBALJS);
            }

            $sHeader = $this->fetch('header.tpl', self::$cacheKey);
            $sMenuTop = $this->fetch('menu-top.tpl', self::$cacheKey);
            $sFooter = $this->fetch('footer.tpl', self::$cacheKey);

            $sView = $this->fetch($pathView, self::$cacheKey);

            if ($return == false) {
                if (OUTPUT_CONTROL) {
                    // Get Unexpected output internal //
                    $unexpected_output_internal = ob_get_contents();

                    ob_clean();

                    // Check Unexpected output //
                    if ($unexpected_output_internal) {
                        Core\Debug::savelogfile(2, 'Unexpected output internal', $unexpected_output_internal);
                        throw new \Exception('Unexpected output internal');
                    }
                }

                switch ($typereturn) {
                    case 'full':
                        echo $sHeader.$sMenuTop.$sView.$sFooter;
                        break;
                    case 'nomenu':
                        echo $sHeader.$sView.$sFooter;
                        break;
                    case 'onlyview':
                        echo $sView;
                        break;
                    default:
                        echo $sHeader.$sMenuTop.$sView.$sFooter;
                        break;
                }

                exit();
            } else {
                switch ($typereturn) {
                    case 'full':
                        return $sHeader.$sMenuTop.$sView.$sFooter;
                        break;
                    case 'nomenu':
                        return $sHeader.$sView.$sFooter;
                        break;
                    case 'onlyview':
                        return $sView;
                        break;
                    default:
                        return $sHeader.$sMenuTop.$sView.$sFooter;
                        break;
                }
            }
        } else {
            throw new \Exception('Error PATH View');
        }
    }

    public function clearSettings()
    {
        self::$css = array();
        self::$js = array();
        self::$jsHead = array();
        self::$extraTplJS = array();
        self::$extraTplJSTop = array();
        self::$sCurrAssetName = 'default';
    }

    public function setCssJsLayout($sAssetName = 'default', $layoutName = 'default')
    {
        $aAssetConfig = AssetConfig::getInstance()->getAssetConfig($sAssetName);

        if (!isset($aAssetConfig['aCssFileLayout']) || !isset($aAssetConfig['aJsFileHeadLayout']) || !isset($aAssetConfig['aJsFileFooterLayout'])) {
            return false;
        }

        self::$sCurrAssetName = $sAssetName;

        $aCssFileLayout = $aAssetConfig['aCssFileLayout'];
        $aJsFileHeadLayout= $aAssetConfig['aJsFileHeadLayout'];
        $aJsFileFooterLayout = $aAssetConfig['aJsFileFooterLayout'];

        // CSS Layout
        if (!empty($aCssFileLayout) && is_array($aCssFileLayout)) {
            foreach ($aCssFileLayout as $key => $fileCss) {
                $pathCssLayout = VIEWS_FOLDER.'/layout/'.$layoutName.'/css/'.$fileCss;
                array_unshift(self::$cssLayout, $pathCssLayout);
            }
        }
        // JS Header Layout
        if (!empty($aJsFileHeadLayout) && is_array($aJsFileHeadLayout)) {
            foreach ($aJsFileHeadLayout as $key => $fileJsHead) {
                $pathJsHeadLayout = VIEWS_FOLDER.'/layout/'.$layoutName.'/js/'.$fileJsHead;
                array_unshift(self::$jsHeadLayout, $pathJsHeadLayout);
            }
        }
        // JS Footer Layout
        if (!empty($aJsFileFooterLayout) && is_array($aJsFileFooterLayout)) {
            foreach ($aJsFileFooterLayout as $key => $fileJsFooter) {
                $pathJsFooterLayout = VIEWS_FOLDER.'/layout/'.$layoutName.'/js/'.$fileJsFooter;
                array_unshift(self::$jsLayout, $pathJsFooterLayout);
            }
        }

        return true;
    }

    public function setJs(array $js, $head = false, $customController = false, $checkUrl = false)
    {
        if (is_string($customController)) {
            $viewController = $customController;
        } else {
            $viewController = self::$controller;
        }

        $countJs = count($js);

        if (is_array($js) && $countJs) {
            for ($i = 0; $i < $countJs; ++$i) {
                if ($head) {
                    self::$jsHead[] = VIEWS_FOLDER.'/'.$viewController.'/js/'.$js[$i];
                } else {
                    self::$js[] = VIEWS_FOLDER.'/'.$viewController.'/js/'.$js[$i];
                }
            }
        } else {
            throw new \Exception('Error JS');
        }
    }

    public function createTmpJs($sAssetName = null, $customController = null, $customMethod = null)
    {

      /* Layout join js */
        if (!is_array(self::$jsHeadLayout)) {
            self::$jsHeadLayout = array();
        }

        if (!is_array(self::$jsLayout)) {
            self::$jsLayout = array();
        }

        if (empty($sAssetName)) {
            $sAssetName = self::$sCurrAssetName;
        }

        $joinJsFileHeadLayout = $this->joinFile(self::$jsHeadLayout, '.js', 'layoutHead-'.$sAssetName);
        $joinJsFileLayout = $this->joinFile(self::$jsLayout, '.js', 'layout-'.$sAssetName);

        if (isset($joinJsFileHeadLayout) && $joinJsFileHeadLayout != false) {
            self::$jsHeadLayout = array();
            self::$jsHeadLayout[] = $joinJsFileHeadLayout;
        }
        if (isset($joinJsFileLayout) && $joinJsFileLayout != false) {
            self::$jsLayout = array();
            self::$jsLayout[] = $joinJsFileLayout;
        }

        /* Template join js */

        if (!is_array(self::$jsHead)) {
            self::$jsHead = array();
        }

        if (!is_array(self::$js)) {
            self::$js = array();
        }

        if (is_string($customController)) {
            $viewController = $customController;
        } else {
            $viewController = self::$controller;
        }
        if (is_string($customMethod)) {
            $viewMethod = $customMethod;
        } else {
            $viewMethod = self::$method;
        }

        $joinJsFileHead = $this->joinFile(self::$jsHead, '.js', $viewController.$viewMethod.'Head');
        $joinJsFile = $this->joinFile(self::$js, '.js', $viewController.$viewMethod);

        if (isset($joinJsFileHead) && $joinJsFileHead != false) {
            self::$jsHead = array();
            self::$jsHead[] = $joinJsFileHead;
        }
        if (isset($joinJsFile) && $joinJsFile != false) {
            self::$js = array();
            self::$js[] = $joinJsFile;
        }
    }

    public function createTmpCss($sAssetName = null, $customController = null, $customMethod = null)
    {
        /* Layout join css */

        if (!is_array(self::$cssLayout)) {
            self::$cssLayout = array();
        }

        if (empty($sAssetName)) {
            $sAssetName = self::$sCurrAssetName;
        }

        $joinCssFileLayout = $this->joinFile(self::$cssLayout, '.css', 'layout-'.$sAssetName);

        if (isset($joinCssFileLayout) && $joinCssFileLayout != false) {
            self::$cssLayout = array(); // reset no join css
            self::$cssLayout[] = $joinCssFileLayout; // add join css
        }

        /* Template join css */

        if (is_string($customController)) {
            $viewController = $customController;
        } else {
            $viewController = self::$controller;
        }

        if (is_string($customMethod)) {
            $viewMethod = $customMethod;
        } else {
            $viewMethod = self::$method;
        }

        if (!is_array(self::$css)) {
            self::$css = array();
        }

        $joinCssFile = $this->joinFile(self::$css, '.css', $viewController.$viewMethod);

        if (isset($joinCssFile) && $joinCssFile != false) {
            self::$css = array(); // reset no join css
            self::$css[] = $joinCssFile; // add join css
        }

        return true;
    }

    public function setJsExternal(array $js, $head = false)
    {
        if (is_array($js) && count($js)) {
            for ($i = 0; $i < count($js); ++$i) {
                $jsFile = $js[$i];

                if ($head) {
                    self::$jsHead[] = $jsFile;
                } else {
                    self::$js[] = $jsFile;
                }
            }
        } else {
            throw new \Exception('Error JS External');
        }
    }

    public function setCssExternal(array $css)
    {
        if (is_array($css) && count($css)) {
            for ($i = 0; $i < count($css); ++$i) {
                $cssFile = $css[$i];

                self::$css[] = $cssFile;
            }
        } else {
            throw new \Exception('Error CSS External');
        }
    }

    public function setCss(array $css, $customController = false, $checkUrl = false)
    {
        if (is_string($customController)) {
            $viewController = $customController;
        } else {
            $viewController = self::$controller;
        }

        if (is_array($css) && count($css)) {
            for ($i = 0; $i < count($css); ++$i) {
                self::$css[] = VIEWS_FOLDER.'/'.$viewController.'/css/'.$css[$i];
            }
        } else {
            throw new \Exception('Error CSS');
        }
    }

    public function setExtraTplJS($nameTplJs = '', $head = false)
    {
        if (is_string($nameTplJs)) {
            if ($head) {
                self::$extraTplJSTop = $nameTplJs;
            } else {
                self::$extraTplJS = $nameTplJs;
            }
        } else {
            throw new \Exception('Error Extra TPL Js');
        }
    }

    private function foldersize($file)
    {
        $size = 0;

        if (is_dir($file)) {
            $rdir = opendir($file);

            while ($cfile = readdir($rdir)) {
                if ($cfile != '.' && $cfile != '..') {
                    $size += filesize($file.'/'.$cfile);
                }
            }
        } else {
            return 0;
        }

        $kbytes = (float) $size / 1024;
        $mbytes = (float) $kbytes / 1024;

        return round($mbytes, 2);
    }

    private function joinFile($aFileOpen = null, $ext = '.css', $outName = 'archivo')
    {

        // ES FUNDAMENTAL LA SEGURIDAD EN ESTE PROGRAMA //
        //define('DS', DIRECTORY_SEPARATOR);
        //define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);
        //define('APPS_FOLDER', 'Apps');

        if (empty($aFileOpen) || !is_array($aFileOpen)) {
            return false;
        }

        $BASE_DIR_APPS = PARTICLE_PATH_APPS;

        $returnNewFile = $BASE_DIR_APPS.'public'.DS.'tmp'.DS.$outName.$ext;
        $urlPublic = BASE_URL_APPS_STATIC.'public/tmp/'.$outName.$ext;

        if (file_exists($returnNewFile)) {
            return $urlPublic;
        }

        $jsCss_code = null;

        foreach ($aFileOpen as $dirFile) {
            $dirFileFull = $BASE_DIR_APPS.$dirFile.$ext;
            if (file_exists($dirFileFull)) {
                $file_handle = fopen($dirFileFull, 'r');
                while (!feof($file_handle)) {
                    $jsCss_code .= fgets($file_handle);
                }
                fclose($file_handle);
            } else {
                return false;
            }
        }

        $fileNew = fopen($returnNewFile, 'a');

        if ($ext === '.js') {
            if (empty($jsCss_code)) {
                fwrite($fileNew, $jsCss_code.PHP_EOL);
                fclose($fileNew);
                return $urlPublic;
            }

            if (TYPEMODE == 'PROD') {
                $post_fields = array(
                  'js_code' => $jsCss_code,
                  'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
                  'output_format' => 'xml',
                  'output_info' => 'compiled_code',
                );

                $ch = curl_init("https://closure-compiler.appspot.com/compile");
                curl_setopt($ch, CURLOPT_POST, count($post_fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $optimized_js = curl_exec($ch);

                $xmlResultCompile = false;
                if (isset($optimized_js) && !empty($optimized_js)) {
                    $xmlResultCompile = simplexml_load_string($optimized_js);
                }

                if ($xmlResultCompile === false || !isset($xmlResultCompile->compiledCode) || empty($xmlResultCompile->compiledCode)) {
                    fwrite($fileNew, $jsCss_code.PHP_EOL);
                } else {
                    fwrite($fileNew, (string)$xmlResultCompile->compiledCode.PHP_EOL);
                }

                curl_close($ch);
            } else {
                fwrite($fileNew, $jsCss_code.PHP_EOL);
            }

            fclose($fileNew);
        } elseif ($ext == '.css') {
            if (empty($jsCss_code)) {
                fwrite($fileNew, $jsCss_code.PHP_EOL);
                fclose($fileNew);
                return $urlPublic;
            }

            if (TYPEMODE == 'PROD') {
                // Force white-space(s) in `calc()`
                if (strpos($jsCss_code, 'calc(') !== false) {
                    $jsCss_code = preg_replace_callback('#(?<=[\s:])calc\(\s*(.*?)\s*\)#', function ($matches) {
                        return 'calc(' . preg_replace('#\s+#', "\x1A", $matches[1]) . ')';
                    }, $jsCss_code);
                }
                // Remove comments
                $jsCss_code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $jsCss_code);
                // Remove space after colons
                $jsCss_code = str_replace(': ', ':', $jsCss_code);
                // Remove whitespace
                $jsCss_code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $jsCss_code);
                // @codingStandardsIgnoreStart
                $minify_css = preg_replace(
                  array(
                      // Remove comment(s)
                      '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                      // Remove unused white-space(s)
                      '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                      // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                      '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                      // Replace `:0 0 0 0` with `:0`
                      '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                      // Replace `background-position:0` with `background-position:0 0`
                      '#(background-position):0(?=[;\}])#si',
                      // Replace `0.6` with `.6`, but only when preceded by a white-space or `=`, `:`, `,`, `(`, `-`
                      '#(?<=[\s=:,\(\-]|&\#32;)0+\.(\d+)#s',
                      // Minify string value
                      '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][-\w]*?)\2(?=[\s\{\}\];,])#si',
                      '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                      // Minify HEX color code
                      '#(?<=[\s=:,\(]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                      // Replace `(border|outline):none` with `(border|outline):0`
                      '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                      // Remove empty selector(s)
                      '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
                      '#\x1A#'),
                array (
                      '$1',
                      '$1$2$3$4$5$6$7',
                      '$1',
                      ':0',
                      '$1:0 0',
                      '.$1',
                      '$1$3',
                      '$1$2$4$5',
                      '$1$2$3',
                      '$1:0',
                      '$1$2',
                      ' '),
                      $jsCss_code
                );
                // @codingStandardsIgnoreEnd
                fwrite($fileNew, $minify_css.PHP_EOL);
            } else {
                fwrite($fileNew, $jsCss_code.PHP_EOL);
            }

            fclose($fileNew);
        } else {
            fclose($fileNew);
            return false;
        }

        return $urlPublic;
        // ES FUNDAMENTAL LA SEGURIDAD EN ESTE PROGRAMA //
    }

    public function mjmlTohtml($contMJML)
    {
        $jsonMJML = json_encode(array('mjml' => (string)$contMJML));

        if (empty($jsonMJML)) {
            return false;
        }

        try {
            $cURL = curl_init();
            curl_setopt($cURL, CURLOPT_URL, "https://api.mjml.io/v1/render");
            curl_setopt($cURL, CURLOPT_POST, 1);
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $jsonMJML);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            //curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($cURL, CURLOPT_TIMEOUT, 0);
            curl_setopt($cURL, CURLOPT_USERPWD, MJML_APPLICATION_ID.':'.MJML_SECRET_KEY);
            $jsonResponse = curl_exec($cURL);
            curl_close($cURL);

            // return to array
            return json_decode($jsonResponse, true);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
