<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Theme+
 * Copyright (C) 2010,2011 InfinitySoft <http://www.infinitysoft.de>
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
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 * @license    LGPL
 */


/**
 * Version 3 alpha1 Warning
 */
if (TL_MODE == 'BE') {
	$objEnvironment = Environment::getInstance();
	if (!$GLOBALS['TL_CONFIG']['theme_plus_alpha_mode']
		// The alpha controller itself
		&& strpos($objEnvironment->requestUri, 'system/modules/theme_plus/AlphaController.php') === false
		// Backend login
		&& strpos($objEnvironment->requestUri, 'contao/index.php') === false
		// Install Tool
		&& strpos($objEnvironment->requestUri, 'contao/install.php') === false
	) {
		$objInput         = Input::getInstance();
		$blnInstallStable = $objInput->post('repository_action') == 'install'
			&& $objInput->post('repository_extension') == 'theme_plus'
			&& $objInput->post('repository_version') < 30000000;

		// if install stable, update the db and remove alpha, beta and rc allow
		if ($blnInstallStable) {
			$objDatabase = Database::getInstance();
			$objDatabase->execute("UPDATE tl_repository_installs SET alpha=0, beta=0, rc=0 WHERE extension='theme_plus'");
		} else {
			header('Location: ' . $objEnvironment->url . $GLOBALS['TL_CONFIG']['websitePath'] . '/system/modules/theme_plus/AlphaController.php');
			exit;
		}
	}
}


/**
 * RC Hack!
 */
if (VERSION == 2.10 && BUILD == 'RC1' && file_exists(TL_ROOT . '/system/modules/theme_plus/config/runonce.php')) {
	class ThemePlusHack extends System
	{
		public function __construct()
		{
		}

		public function run()
		{
			$this->import('Database');
			$this->Database->execute("INSERT INTO tl_runonce (name) VALUES ('system/modules/theme_plus/config/runonce.php')");
		}
	}

	$objThemePlusHack = new ThemePlusHack();
	$objThemePlusHack->run();
}


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
$GLOBALS['TL_CONFIG']['theme_plus_exclude_contaocss'] = '';
$GLOBALS['TL_CONFIG']['theme_plus_exclude_mootools']  = '';
$GLOBALS['TL_CONFIG']['theme_plus_lesscss_mode']      = 'phpless';
$GLOBALS['TL_CONFIG']['css_embed_images']             = 16384;


/**
 * HOOKs
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][]     = array('ThemePlus', 'hookReplaceInsertTags');
$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = array('ThemePlus', 'hookOutputBackendTemplate');


/**
 * Page types
 */
$GLOBALS['TL_PTY']['regular'] = 'ThemePlusPageRegular';


/**
 * easy_themes integration
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_file']     = array
(
	'href_fragment' => 'table=tl_theme_plus_file',
	'icon'          => 'system/modules/theme_plus/html/icon.png'
);
$GLOBALS['TL_EASY_THEMES_MODULES']['theme_plus_variable'] = array
(
	'href_fragment' => 'table=tl_theme_plus_variable',
	'icon'          => 'system/modules/theme_plus/html/variable.png'
);


/**
 * Script frameworks
 */
$GLOBALS['TL_SCRIPT_FRAMEWORKS']['mooSource']['moo_googleapis'] = array('ThemePlus', 'addMooGoogleAPIs');
$GLOBALS['TL_SCRIPT_FRAMEWORKS']['mooSource']['moo_local']      = array('ThemePlus', 'addMooLocal');


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
		while (count($args))
		{
			$temp = array_shift($args);
			foreach ($temp as $v)
			{
				$array[] = $v;
			}
		}
		return $array;
	}
}