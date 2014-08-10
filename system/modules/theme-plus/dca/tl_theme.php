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
 * Table tl_theme
 */
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_stylesheet';
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_javascript';
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_variable';


/**
 * Operations
 */
$intOffset                                           = array_search('css', array_keys($GLOBALS['TL_DCA']['tl_theme']['list']['operations'])) + 1;
$GLOBALS['TL_DCA']['tl_theme']['list']['operations'] = array_merge
(
	array_slice($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 0, $intOffset),
	[
		'theme_plus_stylesheet'     => [
			'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'],
			'href'                => 'table=tl_theme_plus_stylesheet',
			'icon'                => 'assets/theme-plus/images/stylesheet.png',
			'button_callback'     => ['Bit3\Contao\ThemePlus\DataContainer\Theme', 'editStylesheet']
		],
		'theme_plus_javascript'     => [
			'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'],
			'href'                => 'table=tl_theme_plus_javascript',
			'icon'                => 'assets/theme-plus/images/javascript.png',
			'button_callback'     => ['Bit3\Contao\ThemePlus\DataContainer\Theme', 'editJavaScript']
		],
		'theme_plus_variable' => [
			'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'],
			'href'                => 'table=tl_theme_plus_variable',
			'icon'                => 'assets/theme-plus/images/variable.png',
			'button_callback'     => ['Bit3\Contao\ThemePlus\DataContainer\Theme', 'editVariable']
		]
	],
	array_slice($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], $intOffset)
);
