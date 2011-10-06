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

$GLOBALS['TL_DCA']['tl_layout']['config']['onload_callback'][] = array('tl_layout_theme_plus', 'onload');

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(
	'stylesheet',
	'theme_plus_exclude_contaocss,theme_plus_exclude_frameworkcss,stylesheet,theme_plus_stylesheets,theme_plus_javascripts,theme_plus_exclude_files',
	$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_contaocss'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_contaocss'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_frameworkcss'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_frameworkcss'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12', 'submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_files'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files'],
	'inputType'               => 'multitextWizard',
	'eval'                    => array
	(
		'tl_class' => 'clr',
		'style'    => 'width:100%;',
		'columns'  => array
		(
			array
			(
				'label'     => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files'][2],
				'width'     => '600px'
			)
		)
	)
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['stylesheet']['eval']['tl_class'] .= ' clr';

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_stylesheets'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets'],
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getStylesheets'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascripts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts'],
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getJavaScripts'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['mooSource']['eval']['includeBlankOption'] = true;

/**
 * Class tl_layout_theme_plus
 *
 */
class tl_layout_theme_plus extends Backend
{
	public function onload($dc)
	{
		if ($dc) {
			$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE id=?")->execute($dc->id);
			if ($objLayout->next() && $objLayout->theme_plus_exclude_frameworkcss)
			{
				$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(
					';{static_legend},static',
					'',
					$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
				$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['header'] = str_replace(
					'headerHeight', '',
					$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['header']);
				$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['footer'] = str_replace(
					'footerHeight', '',
					$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['footer']);
				$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['cols_2cll'] = str_replace(
					'widthLeft', '',
					$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['cold_2cll']);
				$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['cols_2clr'] = str_replace(
					'widthRight', '',
					$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['cols_2clr']);
				$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['cols_3cl'] = str_replace(
					array('widthLeft', 'widthRight'), '',
					$GLOBALS['TL_DCA']['tl_layout']['subpalettes']['cols_3cl']);
			}
		}
	}
	
	public function getStylesheets()
	{
		return $this->getFiles('css');
	}
	
	public function getJavaScripts()
	{
		return $this->getFiles('js');
	}
	
	public function getFiles($strTypePrefix)
	{
		$arrFile = array();
		
		$objTheme = $this->Database
			->execute("SELECT * FROM tl_theme ORDER BY name");
		while ($objTheme->next())
		{
			$objFile = $this->Database
				->prepare("SELECT * FROM tl_theme_plus_file WHERE pid=? AND (type=? OR type=?) ORDER BY sorting")
				->execute($objTheme->id, $strTypePrefix . '_file', $strTypePrefix . '_url');
			while ($objFile->next())
			{
				$strType = $objFile->type;
				$label = $objFile->$strType;

				switch ($objFile->type) {
				case 'js_file': case 'js_url':
					$image = 'iconJS.gif';
					break;

				case 'css_file': case 'css_url':
					$image = 'iconCSS.gif';
					break;

				default:
					$image = false;
				}

				$arrFile[$objFile->id] = ($image ? $this->generateImage($image, $label, 'style="vertical-align:middle"') . ' ' : '') . '[' . $objTheme->name . '] ' . $label;
			}
		}
		return $arrFile;
	}
}
