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

namespace Bit3\Contao\ThemePlus\DeveloperTool;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Bit3\Contao\ThemePlus\Asset\DelegatorAssetInterface;
use Bit3\Contao\ThemePlus\ThemePlusEnvironment;

class DeveloperTool
{
    /**
     * List of all added files.
     *
     * @var AssetInterface[]
     */
    private $files = [];

    public function registerFile($id, $asset)
    {
        $this->files[$id] = $asset;
    }

    /**
     * @param $strBuffer
     */
    public function inject($strBuffer)
    {
        global $objPage;

        if ($objPage && ThemePlusEnvironment::isDesignerMode()) {
            // search for the layout
            $layout = \LayoutModel::findByPk($objPage->layout);

            $files             = [];
            $stylesheetsCount  = 0;
            $stylesheetsBuffer = '';
            $javascriptsCount  = 0;
            $javascriptsBuffer = '';

            foreach ($this->files as $id => $file) {
                $asset   = $file->asset;
                $files[] = $id;

                while ($asset instanceof DelegatorAssetInterface) {
                    $asset = $asset->getAsset();
                }

                if ($asset instanceof FileAsset) {
                    $sourcePath = $asset->getSourcePath();
                    $fileName   = basename($asset->getSourcePath());
                } else {
                    if ($asset instanceof HttpAsset) {
                        $class    = new \ReflectionClass($asset);
                        $property = $class->getProperty('sourceUrl');
                        $property->setAccessible(true);
                        $sourcePath = $property->getValue($asset);
                        $fileName   = parse_url($sourcePath, PHP_URL_PATH);
                    } else {
                        $sourcePath = $asset->getTargetPath();
                        $fileName   = basename($sourcePath);
                    }
                }

                switch ($file->type) {
                    case 'css':
                        $icon   = '<img src="assets/theme-plus/images/stylesheet.png">';
                        $type   = 'css';
                        $buffer = &$stylesheetsBuffer;
                        $stylesheetsCount++;
                        break;

                    case 'js':
                        $icon   = '<img src="assets/theme-plus/images/javascript.png">';
                        $type   = 'js';
                        $buffer = &$javascriptsBuffer;
                        $javascriptsCount++;
                        break;
                }

                $buffer .= sprintf(
                    '<div id="monitor-%s" class="theme-plus-dev-tool-monitor theme-plus-dev-tool-type-%s theme-plus-dev-tool-loading">'
                    .
                    '%s ' .
                    '<a href="%s" target="_blank" class="theme-plus-dev-tool-link">%s</a>' .
                    '</div>
',
                    $id,
                    $type,
                    $icon,
                    $file->url,
                    $sourcePath
                );
            }

            $strBuffer = preg_replace(
                '~<base[^>]+>~',
                sprintf(
                    '$0
<link rel="stylesheet" href="assets/theme-plus/stylesheets/dev.css">
<script src="assets/theme-plus/javascripts/dev.js"></script>'
                ),
                $strBuffer
            );

            $strBuffer = preg_replace(
                '|<body[^>]*>|',
                sprintf(
                    '$0
<!-- indexer::stop -->
<div id="theme-plus-dev-tool" class="%s">
<div id="theme-plus-dev-tool-toggler" title="Theme+ developers tool">T+</div>
<div id="theme-plus-dev-tool-stylesheets">
  <div id="theme-plus-dev-tool-stylesheets-counter">%s <span id="theme-plus-dev-tool-stylesheets-count">0</span> / <span id="theme-plus-dev-tool-stylesheets-total">%d</span></div>
  <div id="theme-plus-dev-tool-stylesheets-files">%s</div>
</div>
<div id="theme-plus-dev-tool-javascripts">
  <div id="theme-plus-dev-tool-javascripts-counter">%s <span id="theme-plus-dev-tool-javascripts-count">0</span> / <span id="theme-plus-dev-tool-javascripts-total">%d</span></div>
  <div id="theme-plus-dev-tool-javascripts-files">%s</div>
</div>
<div id="theme-plus-dev-tool-exception"></div>
</div>
<script>initThemePlusDevTool(%s, %s);</script>
<!-- indexer::continue -->',
                    \Input::cookie('THEME_PLUS_DEV_TOOL_COLLAPES') == 'no'
                        ? ''
                        : 'theme-plus-dev-tool-collapsed',
                    \Image::getHtml('assets/theme-plus/images/stylesheet.png'),
                    $stylesheetsCount,
                    $stylesheetsBuffer,
                    \Image::getHtml('assets/theme-plus/images/javascript.png'),
                    $javascriptsCount,
                    $javascriptsBuffer,
                    json_encode($files),
                    json_encode((bool) $layout->theme_plus_javascript_lazy_load)
                ),
                $strBuffer
            );
        }

        return $strBuffer;
    }

    /**
     * Generate a debug comment from an asset.
     *
     * @return string
     */
    public function getDebugComment(AssetInterface $asset)
    {
        return '<!-- ' . PHP_EOL . static::getAssetDebugString($asset, '  ') . PHP_EOL . '-->' . PHP_EOL;
    }

    /**
     * Generate a debug string for the asset.
     *
     * @param \Assetic\Asset\AssetInterface $asset
     * @param string                        $depth
     *
     * @return string
     */
    public function getAssetDebugString(AssetInterface $asset, $depth = '')
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
                    /** @var DelegatorAssetInterface $asset */
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
}
