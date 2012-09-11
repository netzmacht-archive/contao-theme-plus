<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_file';
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_variable';

$intOffset                                           = array_search('css', array_keys($GLOBALS['TL_DCA']['tl_theme']['list']['operations'])) + 1;
$GLOBALS['TL_DCA']['tl_theme']['list']['operations'] = array_merge
(
	array_slice($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 0, $intOffset),
	array
	(
		'theme_plus_file'     => array
		(
			'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_file'],
			'href'                => 'table=tl_theme_plus_file',
			'icon'                => 'system/modules/theme_plus/public/icon.png',
			'button_callback'     => array('tl_theme_plus', 'editThemePlusFile')
		),
		'theme_plus_variable' => array
		(
			'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'],
			'href'                => 'table=tl_theme_plus_variable',
			'icon'                => 'system/modules/theme_plus/public/variable.png',
			'button_callback'     => array('tl_theme_plus', 'editThemePlusVariable')
		)
	),
	array_slice($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], $intOffset)
);


/**
 * Class tl_theme_plus
 *
 */
class tl_theme_plus extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	public function editThemePlusFile($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('theme_plus', 'themes')) ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ' : $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
	}


	public function editThemePlusVariable($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('theme_plus', 'themes')) ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ' : $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)) . ' ';
	}
}
