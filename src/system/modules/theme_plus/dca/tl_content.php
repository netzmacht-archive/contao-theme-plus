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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['script_source'] = '{type_legend},type;{script_source_legend},script_source';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['script_source'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['script_source'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_content_theme_plus', 'getJavaScriptFiles'),
	'eval'                    => array('multiple'=> true,
	                                   'tl_class'=> 'clr')
);

/**
 * Class tl_content_theme_plus
 *
 */
class tl_content_theme_plus extends Backend
{
	public function getJavaScriptFiles(DataContainer $dc)
	{
		$objArticle = $this->Database->prepare("SELECT * FROM tl_article WHERE id=?")->execute($dc->activeRecord->pid);
		if (!$objArticle->next()) {
			return array();
		}

		$objPage = $this->getPageDetails($objArticle->pid);
		if (!$objPage->layout) {
			$objLayout = $this->Database->execute("SELECT * FROM tl_layout WHERE fallback='1'");
			if ($objLayout->next()) {
				$objPage->layout = $objLayout->id;
			}
			else
			{
				return array();
			}
		}

		$arrJavaScriptFiles = array();
		$objJavaScriptFiles = $this->Database->prepare("
				SELECT
					s.*
				FROM
					tl_theme_plus_file s
				INNER JOIN
					tl_theme t
				ON
					t.id=s.pid
				INNER JOIN
					tl_layout l
				ON
					t.id = l.pid
				WHERE
					l.id=?
				AND s.type IN ('js_file','js_url')
				ORDER BY
					s.sorting")
			->execute($objPage->layout);
		while ($objJavaScriptFiles->next())
		{
			$strType = $objJavaScriptFiles->type;
			$label   = ' ' . $objJavaScriptFiles->$strType;

			$arrJavaScriptFiles[$objJavaScriptFiles->id] = $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label;
		}
		return $arrJavaScriptFiles;
	}
}
