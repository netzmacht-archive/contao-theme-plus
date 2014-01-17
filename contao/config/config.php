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
 * Config
 */
$GLOBALS['TL_CONFIG']['theme_plus_compile_mode'] = 'immediate';


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
// $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_stylesheet_collection';
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_javascript';
// $GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_javascript_collection';
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_variable';


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['includes']['script_source'] = 'Bit3\Contao\ThemePlus\Hybrid\JavaScript';


/**
 * Content elements
 */
$GLOBALS['TL_CTE']['includes']['script_source'] = 'Bit3\Contao\ThemePlus\Hybrid\JavaScript';


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['initializeTwig']['themeplus']           = array('Bit3\Contao\ThemePlus\TwigExtension', 'init');
$GLOBALS['TL_HOOKS']['parseTemplate']['themeplus']            = array('Bit3\Contao\ThemePlus\ThemePlus', 'hookParseTemplate');
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags']['themeplus'] = array('Bit3\Contao\ThemePlus\ThemePlus', 'hookReplaceDynamicScriptTags');
//$GLOBALS['TL_HOOKS']['outputFrontendTemplate'] = array('Bit3\Contao\ThemePlus\ThemePlus', 'hookOutputFrontendTemplate');
//$GLOBALS['TL_HOOKS']['replaceInsertTags'][]     = array('Bit3\Contao\ThemePlus', 'hookReplaceInsertTags');
//$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = array('Bit3\Contao\ThemePlus', 'hookOutputBackendTemplate');


/**
 * easy_themes integration
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_stylesheet'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'][1],
    'href_fragment' => 'table=tl_theme_plus_stylesheet',
    'icon'          => 'system/modules/theme-plus/assets/images/stylesheet.png',
    'appendRT'      => true,
);
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_javascript'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'][1],
    'href_fragment' => 'table=tl_theme_plus_javascript',
    'icon'          => 'system/modules/theme-plus/assets/images/javascript.png',
    'appendRT'      => true,
);
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_variable']   = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'][1],
    'href_fragment' => 'table=tl_theme_plus_variable',
    'icon'          => 'system/modules/theme-plus/assets/images/variable.png',
    'appendRT'      => true,
);


/**
 * Assetic compiler filter
 */
$GLOBALS['ASSETIC']['compiler']['contaoInsertTag']                = 'Bit3\Contao\ThemePlus\Filter\ContaoInsertTagFilter';
$GLOBALS['ASSETIC']['compiler']['contaoReplaceVariable']          = 'Bit3\Contao\ThemePlus\Filter\ContaoReplaceVariableFilter';
$GLOBALS['ASSETIC']['compiler']['contaoReplaceThemePlusVariable'] = 'Bit3\Contao\ThemePlus\Filter\ContaoReplaceThemePlusVariableFilter';


/**
 * Assetic css compatible filters
 */
$GLOBALS['ASSETIC']['css'][] = 'contaoInsertTag';
$GLOBALS['ASSETIC']['css'][] = 'contaoReplaceVariable';
$GLOBALS['ASSETIC']['css'][] = 'contaoReplaceThemePlusVariable';


/**
 * Assetic js compatible filters
 */
$GLOBALS['ASSETIC']['js'][] = 'contaoInsertTag';
$GLOBALS['ASSETIC']['js'][] = 'contaoReplaceVariable';
$GLOBALS['ASSETIC']['js'][] = 'contaoReplaceThemePlusVariable';


/**
 * Mime types
 */
$GLOBALS['TL_MIME']['less'] = array('text/x-less', 'iconCSS.gif');
$GLOBALS['TL_MIME']['sass'] = array('text/x-sass', 'iconCSS.gif');
$GLOBALS['TL_MIME']['scss'] = array('text/x-scss', 'iconCSS.gif');
