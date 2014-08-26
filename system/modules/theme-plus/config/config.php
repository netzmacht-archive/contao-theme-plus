<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_theme_plus_javascript'] = 'Bit3\Contao\ThemePlus\Model\JavaScriptModel';
$GLOBALS['TL_MODELS']['tl_theme_plus_stylesheet'] = 'Bit3\Contao\ThemePlus\Model\StylesheetModel';
$GLOBALS['TL_MODELS']['tl_theme_plus_variable']   = 'Bit3\Contao\ThemePlus\Model\VariableModel';


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_stylesheet';
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_javascript';
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_variable';


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
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_variable']   = [
	'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'][0],
	'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'][1],
	'href_fragment' => 'table=tl_theme_plus_variable',
	'icon'          => 'assets/theme-plus/images/variable.png',
	'appendRT'      => true,
];


/**
 * Assetic compiler filter
 */
$GLOBALS['ASSETIC']['compiler']['contaoReplaceVariable']          = 'Bit3\Contao\ThemePlus\Filter\ContaoReplaceVariableFilter';
$GLOBALS['ASSETIC']['compiler']['contaoReplaceThemePlusVariable'] = 'Bit3\Contao\ThemePlus\Filter\ContaoReplaceThemePlusVariableFilter';


/**
 * Assetic css compatible filters
 */
$GLOBALS['ASSETIC']['css'][] = 'contaoReplaceVariable';
$GLOBALS['ASSETIC']['css'][] = 'contaoReplaceThemePlusVariable';


/**
 * Assetic js compatible filters
 */
$GLOBALS['ASSETIC']['js'][] = 'contaoReplaceVariable';
$GLOBALS['ASSETIC']['js'][] = 'contaoReplaceThemePlusVariable';


/**
 * Mime types
 */
$GLOBALS['TL_MIME']['less'] = ['text/x-less', 'iconCSS.gif'];
$GLOBALS['TL_MIME']['sass'] = ['text/x-sass', 'iconCSS.gif'];
$GLOBALS['TL_MIME']['scss'] = ['text/x-scss', 'iconCSS.gif'];
