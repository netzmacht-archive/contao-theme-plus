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
			'icon'                => 'system/modules/theme_plus/html/icon.png',
			'button_callback'     => array('tl_theme_plus', 'editThemePlusFile')
		),
		'theme_plus_variable' => array
		(
			'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_variable'],
			'href'                => 'table=tl_theme_plus_variable',
			'icon'                => 'system/modules/theme_plus/html/variable.png',
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
