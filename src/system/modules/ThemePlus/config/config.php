<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2012 InfinitySoft <http://www.infinitysoft.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_theme_plus_javascript'] = 'ThemePlus\Model\JavaScriptModel';
$GLOBALS['TL_MODELS']['tl_theme_plus_stylesheet'] = 'ThemePlus\Model\StylesheetModel';
$GLOBALS['TL_MODELS']['tl_theme_plus_variable']   = 'ThemePlus\Model\VariableModel';

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
$GLOBALS['FE_MOD']['includes']['script_source'] = 'ThemePlus\Hybrid\JavaScript';


/**
 * Content elements
 */
$GLOBALS['TL_CTE']['includes']['script_source'] = 'ThemePlus\Hybrid\JavaScript';


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['parseTemplate'][]            = array('ThemePlus\ThemePlus', 'hookParseTemplate');
$GLOBALS['TL_HOOKS']['replaceDynamicScriptTags'][] = array('ThemePlus\ThemePlus', 'hookReplaceDynamicScriptTags');
//$GLOBALS['TL_HOOKS']['outputFrontendTemplate'] = array('ThemePlus\ThemePlus', 'hookOutputFrontendTemplate');
//$GLOBALS['TL_HOOKS']['replaceInsertTags'][]     = array('ThemePlus', 'hookReplaceInsertTags');
//$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = array('ThemePlus', 'hookOutputBackendTemplate');


/**
 * easy_themes integration
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_stylesheet'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'][1],
    'href_fragment' => 'table=tl_theme_plus_stylesheet',
    'icon'          => 'system/modules/ThemePlus/assets/images/stylesheet.png',
    'appendRT'      => true,
);
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_javascript'] = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'][1],
    'href_fragment' => 'table=tl_theme_plus_javascript',
    'icon'          => 'system/modules/ThemePlus/assets/images/javascript.png',
    'appendRT'      => true,
);
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_variable']   = array
(
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'][0],
    'title'         => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'][1],
    'href_fragment' => 'table=tl_theme_plus_variable',
    'icon'          => 'system/modules/ThemePlus/assets/images/variable.png',
    'appendRT'      => true,
);


/**
 * Assetic compiler filter
 */
$GLOBALS['ASSETIC']['compiler']['contaoInsertTag']                = 'ThemePlus\Filter\ContaoInsertTagFilter';
$GLOBALS['ASSETIC']['compiler']['contaoReplaceVariable']          = 'ThemePlus\Filter\ContaoReplaceVariableFilter';
$GLOBALS['ASSETIC']['compiler']['contaoReplaceThemePlusVariable'] = 'ThemePlus\Filter\ContaoReplaceThemePlusVariableFilter';


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
$GLOBALS['TL_MIME']['less'] = array('text/css', 'iconCSS.gif');


/**
 * Helper function
 */
if (!function_exists('array_concat')) {
    function array_concat()
    {
        $args  = func_get_args();
        $array = array_shift($args);
        while (count($args)) {
            $temp = array_shift($args);
            foreach ($temp as $v) {
                $array[] = $v;
            }
        }
        return $array;
    }
}