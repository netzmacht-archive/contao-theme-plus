<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_additional_source';


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
$GLOBALS['TL_CONFIG']['additional_sources_combination']             = 'combine_local';
$GLOBALS['TL_CONFIG']['additional_sources_css_compression']         = 'yui';
$GLOBALS['TL_CONFIG']['additional_sources_js_compression']          = 'yui';
$GLOBALS['TL_CONFIG']['additional_sources_yui_cmd']                 = 'yui-compressor';
$GLOBALS['TL_CONFIG']['additional_sources_gz_compression_disabled'] = '';
$GLOBALS['TL_CONFIG']['additional_sources_hide_cssmin_message']     = false;
$GLOBALS['TL_CONFIG']['additional_sources_hide_jsmin_message']      = false;


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['generatePage'][] = array('LayoutAdditionalSources', 'generatePage');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('LayoutAdditionalSources', 'hookReplaceInsertTags');
if (	!$GLOBALS['TL_CONFIG']['additional_sources_hide_cssmin_message']
	||	!$GLOBALS['TL_CONFIG']['additional_sources_hide_jsmin_message'])
{
	$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('LayoutAdditionalSourcesBackend', 'hookParseBackendTemplate');
}


/**
 * easy_themes integration
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['additional_source'] = array
(
	'href_fragment' => 'table=tl_additional_source',
	'icon'          => 'system/modules/layout_additional_sources/html/additional_source.png'
);


?>
