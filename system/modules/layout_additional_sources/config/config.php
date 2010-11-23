<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_additional_source';


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['generatePage'][] = array('LayoutAdditionalSources', 'generatePage');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('LayoutAdditionalSources', 'hookReplaceInsertTags');


/**
 * Settings
 */
$GLOBALS['TL_CONFIG']['additional_sources_combination'] = 'combine_all';
$GLOBALS['TL_CONFIG']['yui_cmd'] = 'yui-compressor';
$GLOBALS['TL_CONFIG']['yui_compression_disabled'] = '';
$GLOBALS['TL_CONFIG']['gz_compression_disabled'] = '';


/**
 * runonce job
 */
$strExecutionLockFile = 'system/modules/layout_additional_sources/config/runonce-1.5.0_stable.lock';
if (!file_exists(TL_ROOT . '/' . $strExecutionLockFile))
{
	# load the runonce class
	require_once(TL_ROOT . '/system/modules/layout_additional_sources/LayoutAdditionalSourcesRunonceJob.php');
	# execute the runonce update job
	LayoutAdditionalSourcesRunonceJob::getInstance()->run("1.5.0 stable");
	# lock the update
	$objLock = new File($strExecutionLockFile);
	$objLock->write('1');
}

?>
