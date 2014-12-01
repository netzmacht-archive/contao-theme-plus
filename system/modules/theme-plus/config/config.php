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
$GLOBALS['TL_HOOKS']['parseTemplate']['themeplus']            = [
    'Bit3\Contao\ThemePlus\ThemePlus',
    'hookParseTemplate'
];
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags']['themeplus'] = [
    'Bit3\Contao\ThemePlus\ThemePlus',
    'hookReplaceDynamicScriptTags'
];


/**
 * Event subscriber
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\ThemePlus\AssetOrganizerSubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\ThemePlus\StaticUrlSubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\ThemePlus\StylesheetCollectorSubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\ThemePlus\StylesheetRendererSubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\ThemePlus\JavaScriptCollectorSubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Bit3\Contao\ThemePlus\JavaScriptRendererSubscriber';


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
