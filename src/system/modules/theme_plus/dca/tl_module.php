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


$GLOBALS['TL_DCA']['tl_module']['palettes']['script_source'] = '{title_legend},name,type;{script_source_legend},script_source';
$GLOBALS['TL_DCA']['tl_module']['fields']['script_source']   = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['script_source'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_theme_plus', 'getJavaScriptFiles'),
	'eval'                    => array('multiple'=> true,
	                                   'tl_class'=> 'clr')
);

/**
 * Class tl_module_theme_plus
 *
 */
class tl_module_theme_plus extends Backend
{
	public function getJavaScriptFiles(DataContainer $dc)
	{
		$objTheme = $this->Database->prepare("SELECT * FROM tl_theme WHERE id=?")->execute($dc->activeRecord->pid);
		if (!$objTheme->next()) {
			return array();
		}

		$arrJavaScriptFiles = array();
		$objJavaScriptFiles = $this->Database->prepare("
				SELECT
					s.*
				FROM
					tl_theme_plus_file s
				WHERE
					s.pid=?
				AND s.type IN ('js_file','js_url')
				ORDER BY
					s.sorting")
			->execute($objTheme->id);
		while ($objJavaScriptFiles->next())
		{
			$strType = $objJavaScriptFiles->type;
			$label   = ' ' . $objJavaScriptFiles->$strType;

			$arrJavaScriptFiles[$objJavaScriptFiles->id] = $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label;
		}
		return $arrJavaScriptFiles;
	}
}
