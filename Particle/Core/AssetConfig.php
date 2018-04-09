<?php

namespace Particle\Core;

final class AssetConfig
{
    private $AssetConfig = array();
    private static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    final private function add($sAssetName, $aCssFileLayout, $aJsFileHeadLayout, $aJsFileFooterLayout)
    {
        $this->AssetConfig[$sAssetName] = array(
          'aCssFileLayout' => $aCssFileLayout,
          'aJsFileHeadLayout' => $aJsFileHeadLayout,
          'aJsFileFooterLayout' => $aJsFileFooterLayout,
        );
    }

    final public function init()
    {
        $this->AssetConfig = array();

        $pathAssetIni = PARTICLE_PATH_APPS.VIEWS_FOLDER.DS.'layout'.DS.'asset.ini';
        if (!is_readable($pathAssetIni)) {
            return false;
        }
        $aAssetIni = parse_ini_file($pathAssetIni, true, INI_SCANNER_TYPED);
        if (empty($aAssetIni)) {
            return false;
        }
        foreach ($aAssetIni as $nameAsset => $itemAsset) {
            $sAssetName = $nameAsset;
            if (!isset($itemAsset['aCssFileLayout']) || empty($itemAsset['aCssFileLayout'])) {
                $itemAsset['aCssFileLayout'] = array();
            }
            if (!isset($itemAsset['aJsFileHeadLayout']) || empty($itemAsset['aJsFileHeadLayout'])) {
                $itemAsset['aJsFileHeadLayout'] = array();
            }
            if (!isset($itemAsset['aJsFileFooterLayout']) || empty($itemAsset['aJsFileFooterLayout'])) {
                $itemAsset['aJsFileFooterLayout'] = array();
            }

            $aCssFileLayout = $itemAsset['aCssFileLayout'];
            $aJsFileHeadLayout = $itemAsset['aJsFileHeadLayout'];
            $aJsFileFooterLayout = $itemAsset['aJsFileFooterLayout'];

            $this->add($sAssetName, $aCssFileLayout, $aJsFileHeadLayout, $aJsFileFooterLayout);
        }

        return true;
    }

    final public function getAssetConfig($sAssetName)
    {
        if (isset($this->AssetConfig[$sAssetName])) {
            return $this->AssetConfig[$sAssetName];
        }

        return false;
    }
}
