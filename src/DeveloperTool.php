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

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Bit3\Contao\ThemePlus\Asset\DelegatorAssetInterface;
use Bit3\Contao\ThemePlus\DataContainer\File;

class DeveloperTool
{
    const BROWSER_IDENT_OVERWRITE = 'THEME_PLUS_BROWSER_IDENT_OVERWRITE';

    /**
     * List of all added files.
     *
     * @var AssetInterface[]
     */
    protected $files = [];

    /**
     * @var array|string[]
     */
    protected $excludeList = [];

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
            if (\Input::post('FORM_SUBMIT') == 'theme_plus_dev_tool') {
                $session = \Session::getInstance();

                $platform = \Input::post('theme_plus_dev_tool_platform');
                $system   = \Input::post('theme_plus_dev_tool_system');
                $browser  = \Input::post('theme_plus_dev_tool_browser');
                $version  = \Input::post('theme_plus_dev_tool_version');

                if ($platform || $system || $browser || $version) {
                    $browserIdentOverwrite = (object) [
                        'platform' => $platform,
                        'system'   => $system,
                        'browser'  => $browser,
                        'version'  => $version,
                    ];
                    $session->set(self::BROWSER_IDENT_OVERWRITE, json_encode($browserIdentOverwrite));
                } else {
                    $session->set(self::BROWSER_IDENT_OVERWRITE, null);
                }

                \Controller::reload();
            }

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

            $browserIdentOverwrite = json_decode(
                \Session::getInstance()->get(self::BROWSER_IDENT_OVERWRITE)
            );

            $fileDataContainer = new File();

            $filterSystems = ['<option value="">System</option>'];
            foreach ($fileDataContainer->getSystems() as $system) {
                $filterSystems[] = sprintf(
                    '<option value="%1$s"%2$s>%1$s</option>',
                    $system,
                    $browserIdentOverwrite && $browserIdentOverwrite->system == $system
                        ? ' selected'
                        : ''
                );
            }

            $filterBrowsers = ['<option value="">Browser</option>'];
            foreach ($fileDataContainer->getBrowsers() as $browser) {
                $filterBrowsers[] = sprintf(
                    '<option value="%1$s">%1$s</option>',
                    $browser,
                    $browserIdentOverwrite && $browserIdentOverwrite->browser == $browser
                        ? ' selected'
                        : ''
                );
            }

            \Controller::loadLanguageFile('tl_theme_plus_filter');

            $filterPlatforms = ['<option value="">Platform</option>'];
            foreach (['desktop', 'tablet', 'tablet-or-mobile', 'mobile'] as $platform) {
                $filterPlatforms[] = sprintf(
                    '<option value="%1$s"%3$s>%2$s</option>',
                    $platform,
                    $GLOBALS['TL_LANG']['tl_theme_plus_filter'][$platform],
                    $browserIdentOverwrite && $browserIdentOverwrite->platform == $platform
                        ? ' selected'
                        : ''
                );
            }

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
<div id="theme-plus-dev-tool-filter">
  <form method="post" action="%s">
		<input type="hidden" name="REQUEST_TOKEN" value="%s">
		<input type="hidden" name="FORM_SUBMIT" value="theme_plus_dev_tool">
		<select id="theme-plus-dev-tool-filter-platform" name="theme_plus_dev_tool_platform" onchange="this.form.submit()">%s</select>
		<select id="theme-plus-dev-tool-filter-system" name="theme_plus_dev_tool_system" onchange="this.form.submit()">%s</select>
		<select id="theme-plus-dev-tool-filter-browser" name="theme_plus_dev_tool_browser" onchange="this.form.submit()">%s</select>
		<input id="theme-plus-dev-tool-filter-version" name="theme_plus_dev_tool_version" value="%s" placeholder="Version" size="4">
		&nbsp;
		<input id="theme-plus-dev-tool-filter-apply" type="submit" value="&raquo;">
  </form>
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
                    \Environment::get('request'),
                    REQUEST_TOKEN,
                    implode('', $filterPlatforms),
                    implode('', $filterSystems),
                    implode('', $filterBrowsers),
                    $browserIdentOverwrite ? $browserIdentOverwrite->version : '',
                    json_encode($files),
                    json_encode((bool) $layout->theme_plus_javascript_lazy_load)
                ),
                $strBuffer
            );
        }

        return $strBuffer;
    }
}
