<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_file';
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_theme_plus_variable';


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
$GLOBALS['TL_CONFIG']['theme_plus_lesscss_mode']            = 'less.js';
$GLOBALS['TL_CONFIG']['theme_plus_gz_compression_disabled'] = '';


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('ThemePlus', 'hookReplaceInsertTags');


/**
 * Page types
 */
$GLOBALS['TL_PTY']['regular'] = 'ThemePlusPageRegular';


/**
 * easy_themes integration
 */
$intOffset = array_search('css', array_keys($GLOBALS['TL_EASY_THEMES_MODULES'])) + 1;
$GLOBALS['TL_EASY_THEMES_MODULES'] = array_merge
(
	array_slice($GLOBALS['TL_EASY_THEMES_MODULES'], 0, $intOffset),
	array
	(
		'theme_plus_file' => array
		(
			'href_fragment' => 'table=tl_theme_plus_file',
			'icon'          => 'system/modules/theme_plus/html/icon.png'
		),
		'theme_plus_variable' => array
		(
			'href_fragment' => 'table=tl_theme_plus_variable',
			'icon'          => 'system/modules/theme_plus/html/variable.png'
		)
	),
	array_slice($GLOBALS['TL_EASY_THEMES_MODULES'], $intOffset)
);

?>
