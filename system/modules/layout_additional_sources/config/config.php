<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Layout Additional Sources
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 * 
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    LGPL
 * @filesource
 */


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
