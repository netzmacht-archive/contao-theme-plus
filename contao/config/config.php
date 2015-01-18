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


/**
 * Configuration
 */
$GLOBALS['TL_CONFIG']['theme_plus_disabled_advanced_asset_caching'] = false;


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_theme_plus_javascript'] = 'Bit3\Contao\ThemePlus\Model\JavaScriptModel';
$GLOBALS['TL_MODELS']['tl_theme_plus_stylesheet'] = 'Bit3\Contao\ThemePlus\Model\StylesheetModel';


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_stylesheet';
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_javascript';


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['initializeSystem']['theme_plus_backend']                 = [
    'Bit3\Contao\ThemePlus\BackendIntegration',
    'hookInitializeSystem'
];
$GLOBALS['TL_HOOKS']['parseTemplate']['theme_plus_javascripts']                = [
    'Bit3\Contao\ThemePlus\JavaScriptsHandler',
    'hookParseTemplate'
];
$GLOBALS['TL_HOOKS']['replaceInsertTags']['theme_plus']                        = [
    'Bit3\Contao\ThemePlus\ThemePlus',
    'replaceCachedAssetInsertTag'
];
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags']['theme_plus'] = [
    'Bit3\Contao\ThemePlus\ThemePlus',
    'disablePageCache'
];
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags']['theme_plus_stylesheets']     = [
    'Bit3\Contao\ThemePlus\StylesheetsHandler',
    'hookReplaceDynamicScriptTags'
];
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags']['theme_plus_javascripts']     = [
    'Bit3\Contao\ThemePlus\JavaScriptsHandler',
    'hookReplaceDynamicScriptTags'
];
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags']['theme_plus_developer_tools'] = [
    'Bit3\Contao\ThemePlus\ThemePlus',
    'injectDeveloperTools'
];
$GLOBALS['TL_HOOKS']['outputBackendTemplate']['theme_plus_backend']            = [
    'Bit3\Contao\ThemePlus\BackendIntegration',
    'hookOutputBackendTemplate'
];


/**
 * Maintenance
 */
$GLOBALS['TL_MAINTENANCE'] = array_merge(
    array_slice($GLOBALS['TL_MAINTENANCE'], 0, 2),
    ['theme_plus' => 'Bit3\Contao\ThemePlus\Maintenance\BuildAssetCache'],
    array_slice($GLOBALS['TL_MAINTENANCE'], 2)
);


/**
 * easy_themes integration
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_stylesheet'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'][1],
    'href_fragment' => 'table=tl_theme_plus_stylesheet',
    'icon'          => 'assets/theme-plus/images/stylesheet.png',
    'appendRT'      => true,
];
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_javascript'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'][1],
    'href_fragment' => 'table=tl_theme_plus_javascript',
    'icon'          => 'assets/theme-plus/images/javascript.png',
    'appendRT'      => true,
];


/**
 * Mime types
 */
$GLOBALS['TL_MIME']['less'] = ['text/x-less', 'iconCSS.gif'];
$GLOBALS['TL_MIME']['sass'] = ['text/x-sass', 'iconCSS.gif'];
$GLOBALS['TL_MIME']['scss'] = ['text/x-scss', 'iconCSS.gif'];
