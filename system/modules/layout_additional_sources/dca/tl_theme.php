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
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_additional_source';
$GLOBALS['TL_DCA']['tl_theme']['list']['operations']['additional_source'] = array
(
	'label'               => &$GLOBALS['TL_LANG']['tl_theme']['additional_source'],
	'href'                => 'table=tl_additional_source',
	'icon'                => 'system/modules/layout_additional_sources/html/additional_source.png',
	'button_callback'     => array('tl_theme_additional_source', 'editAdditionalSource')
);


/**
 * Class tl_theme_additional_source
 *
 */
class tl_theme_additional_source extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	
	public function editAdditionalSource($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('additional_source', 'themes')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)).' ';
	}
}
?>