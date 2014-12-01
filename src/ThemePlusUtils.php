<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

namespace Bit3\Contao\ThemePlus;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Bit3\Contao\ThemePlus\Asset\DelegatorAssetInterface;

class ThemePlusUtils
{
    /**
     * Check filter settings.
     *
     * @param null   $system
     * @param null   $browser
     * @param string $browserVersionComparator
     * @param null   $browserVersion
     * @param null   $platform
     * @param bool   $invert
     *
     * @return bool
     *
     * @deprecated
     */
    public static function checkFilter(
        $system = null,
        $browser = null,
        $browserVersionComparator = '=',
        $browserVersion = null,
        $platform = null,
        $invert = false
    ) {
        $browserIdentOverwrite = json_decode(
            \Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
        );

        $match = true;

        if (!empty($system)) {
            if ($browserIdentOverwrite && $browserIdentOverwrite->system) {
                $currentSystem = $browserIdentOverwrite->system;
            } else {
                $currentSystem = ThemePlusEnvironment::getBrowserDetect()
                    ->getPlatform();
            }

            $match = $match && $currentSystem == $system;
        }
        if (!empty($browser)) {
            if ($browserIdentOverwrite && $browserIdentOverwrite->browser) {
                $currentBrowser = $browserIdentOverwrite->browser;
            } else {
                $currentBrowser = ThemePlusEnvironment::getBrowserDetect()
                    ->getBrowser();
            }

            if (!empty($browserVersionComparator) && !empty($browserVersion)) {
                if ($browserIdentOverwrite && $browserIdentOverwrite->version) {
                    $currentBrowserVersion = $browserIdentOverwrite->version;
                } else {
                    $currentBrowserVersion = ThemePlusEnvironment::getBrowserDetect()
                        ->getVersion();
                }

                switch ($browserVersionComparator) {
                    case 'lt':
                        $browserVersionComparator = '<';
                        break;
                    case 'lte':
                        $browserVersionComparator = '<=';
                        break;
                    case 'gte':
                        $browserVersionComparator = '>=';
                        break;
                    case 'gt':
                        $browserVersionComparator = '>';
                        break;
                }

                $match = $match
                         && $currentBrowser == $browser
                         && version_compare($currentBrowserVersion, $browserVersion, $browserVersionComparator);
            } else {
                $match = $match && $currentBrowser == $browser;
            }
        }
        if (!empty($platform)) {
            switch ($platform) {
                case 'desktop':
                    $match = $match && ThemePlusEnvironment::isDesktop();
                    break;

                case 'tablet':
                    $match = $match && ThemePlusEnvironment::isTablet();
                    break;

                case 'tablet-or-mobile':
                    $match = $match && (ThemePlusEnvironment::isTablet() || ThemePlusEnvironment::isMobile());
                    break;

                case 'mobile':
                    $match = $match && ThemePlusEnvironment::isMobile();
                    break;
            }
        }

        if ($invert) {
            $match = !$match;
        }
        return $match;
    }

    /**
     * Check the file browser filter settings against the request browser.
     *
     * @param \Model $file
     *
     * @return bool
     */
    public static function checkBrowserFilter(\Model\Collection $file)
    {
        if ($file->filter) {
            $rules = deserialize($file->filterRule, true);

            foreach ($rules as $rule) {
                if (static::checkFilterRule($rule)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public static function checkFilterRule($rule)
    {
        return self::checkFilter(
            $rule['system'],
            $rule['browser'],
            $rule['comparator'],
            $rule['browser_version'],
            $rule['platform'],
            $rule['invert']
        );
    }

    /**
     * Generate a debug string for the asset.
     *
     * @param \Assetic\Asset\AssetInterface $asset
     * @param string                        $depth
     *
     * @return string
     *
     * @deprecated Still used in the proxy.php!!!
     */
    public static function getAssetDebugString(AssetInterface $asset, $depth = '')
    {
        $filters = [];
        foreach ($asset->getFilters() as $v) {
            $filters[] = get_class($v);
        }

        if ($asset instanceof AssetCollectionInterface) {
            /** @var AssetCollectionInterface $asset */
            $buffer = $depth . 'collection(' . get_class($asset) . ') {' . PHP_EOL;

            if ($asset->getTargetPath()) {
                $buffer .= $depth . '  target path: ' . $asset->getTargetPath() . PHP_EOL;
            }
            if (count($asset->getFilters())) {
                $buffer .= $depth . '  filters: [' . PHP_EOL;

                foreach ($asset->getFilters() as $filter) {
                    $buffer .= $depth . '    ' . get_class($filter) . PHP_EOL;
                }

                $buffer .= $depth . '  ]' . PHP_EOL;
            }
            $buffer .= $depth . '  last modified: ' . $asset->getLastModified() . PHP_EOL;

            $buffer .= $depth . '  elements: [' . PHP_EOL;
            foreach ($asset->all() as $child) {
                $buffer .= static::getAssetDebugString($child, $depth . '    ') . PHP_EOL;
            }

            $buffer .= $depth . '}';
            return $buffer;
        } else {
            if ($asset instanceof DelegatorAssetInterface) {
                /** @var AssetCollectionInterface $asset */
                $buffer = $depth . 'delegator(' . get_class($asset) . ') {' . PHP_EOL;
                if ($asset instanceof DelegatorAssetInterface) {
                    $buffer .= $depth . '  delegate: [' . PHP_EOL;
                    $buffer .= static::getAssetDebugString($asset->getAsset(), $depth . '    ') . PHP_EOL;
                    $buffer .= $depth . '  ]' . PHP_EOL;
                }
                $buffer .= $depth . '}';
                return $buffer;
            } else {
                /** @var AssetCollectionInterface $asset */
                $buffer = $depth . 'asset(' . get_class($asset) . ') {' . PHP_EOL;
                $buffer .= $depth . '  source path: ' . $asset->getSourcePath() . PHP_EOL;
                $buffer .= $depth . '  source root: ' . $asset->getSourceRoot() . PHP_EOL;

                if ($asset->getTargetPath()) {
                    $buffer .= $depth . '  target path: ' . $asset->getTargetPath() . PHP_EOL;
                }
                if (count($asset->getFilters())) {
                    $buffer .= $depth . '  filters: [' . PHP_EOL;

                    foreach ($asset->getFilters() as $filter) {
                        $buffer .= $depth . '    ' . get_class($filter) . PHP_EOL;
                    }

                    $buffer .= $depth . '  ]' . PHP_EOL;
                }

                $buffer .= $depth . '  last modified: ' . $asset->getLastModified() . PHP_EOL;

                $buffer .= $depth . '}';
                return $buffer;
            }
        }
    }

    /**
     * Wrap the conditional comment around.
     *
     * @param string $html The html to wrap around.
     * @param string $cc   The cc that should wrapped.
     *
     * @return string
     */
    public static function wrapCc($html, $cc)
    {
        if (strlen($cc)) {
            return '<!--[if ' . $cc . ']>' . $html . '<![endif]-->';
        }
        return $html;
    }

    /**
     * Detect gzip data end decode it.
     *
     * @param mixed $varData
     */
    public static function decompressGzip($varData)
    {
        if ($varData[0] == 31 && $varData[0] == 139 && $varData[0] == 8
        ) {
            return gzdecode($varData);
        } else {
            return $varData;
        }
    }


    /**
     * Handle
     *
     * @charset and remove the rule.
     */
    public static function handleCharset($strContent)
    {
        if (preg_match(
            '#\@charset\s+[\'"]([\w\-]+)[\'"]\;#Ui',
            $strContent,
            $arrMatch
        )
        ) {
            // convert character encoding to utf-8
            if (strtoupper($arrMatch[1]) != 'UTF-8') {
                $strContent = iconv(
                    strtoupper($arrMatch[1]),
                    'UTF-8',
                    $strContent
                );
            }
            // remove all @charset rules
            $strContent = preg_replace(
                '#\@charset\s+.*\;#Ui',
                '',
                $strContent
            );
        }
        return $strContent;
    }

    /**
     * Wrap a javascript src for lazy include.
     *
     * @return string
     */
    public static function wrapJavaScriptLazyInclude($strSrc)
    {
        return 'loadAsync(' . json_encode($strSrc) . (ThemePlus::getInstance()
            ->isDesignerMode() ? ', ' . json_encode(md5($strSrc)) : '') . ');';
    }


    /**
     * Wrap a javascript src for lazy embedding.
     *
     * @return string
     */
    public static function wrapJavaScriptLazyEmbedded($strSource)
    {
        $strBuffer = 'var f=(function(){';
        $strBuffer .= $strSource;
        $strBuffer .= '});';
        $strBuffer .= 'if (window.attachEvent){';
        $strBuffer .= 'window.attachEvent("onload",f);';
        $strBuffer .= '}else{';
        $strBuffer .= 'window.addEventListener("load",f,false);';
        $strBuffer .= '}';
        return $strBuffer;
    }
}
