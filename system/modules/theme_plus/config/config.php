<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_file';


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['includes']['script_source'] = 'ScriptSource';


/**
 * Content elements
 */
$GLOBALS['TL_CTE']['includes']['script_source'] = 'ScriptSource';


/**
 * Settings
 */
$GLOBALS['TL_CONFIG']['theme_plus_aggregate_externals']     = '';
$GLOBALS['TL_CONFIG']['theme_plus_lesscss_mode']            = 'less.js';
$GLOBALS['TL_CONFIG']['theme_plus_gz_compression_disabled'] = '';


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['generatePage'][]      = array('ThemePlus', 'generatePage');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('ThemePlus', 'hookReplaceInsertTags');


/**
 * easy_themes integration
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus'] = array
(
	'href_fragment' => 'table=tl_theme_plus',
	'icon'          => 'system/modules/theme_plus/html/icon.png'
);


?>
