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


// add onload callback
$GLOBALS['TL_DCA']['tl_layout']['config']['onload_callback'][] = array('tl_layout_theme_plus', 'onload');


// extend the palette
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = preg_replace(
	array(
	    '#stylesheet#',
		'#mootools#',
		'#(\{expert_legend:hide\}.*);#U'
	),
	array(
		'theme_plus_exclude_contaocss,' . (version_compare(VERSION, '2.11', '<') ? 'theme_plus_exclude_frameworkcss,' : '') . 'stylesheet,theme_plus_stylesheets',
		'theme_plus_javascript_lazy_load,theme_plus_default_javascript_position,theme_plus_javascripts,mootools',
		'$1,theme_plus_exclude_files;'
	),
	$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);


// add field theme_plus_exclude_contaocss
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_contaocss'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_contaocss'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);


// add field theme_plus_exclude_frameworkcss
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_frameworkcss'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_frameworkcss'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12', 'submitOnChange'=>true)
);


// clear float with stylesheet field
$GLOBALS['TL_DCA']['tl_layout']['fields']['stylesheet']['eval']['tl_class'] .= ' clr';


// add field theme_plus_stylesheets
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_stylesheets'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets'],
	'inputType'               => 'fancyCheckboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getStylesheets'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);


// add field theme_plus_javascript_lazy_load
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascript_lazy_load'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascript_lazy_load'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);


// add field theme_plus_default_files_position
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_default_javascript_position'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_default_javascript_position'],
	'inputType'               => 'select',
	'options'                 => array('head', 'head+body', 'body'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_layout']['positions'],
	'eval'                    => array('tl_class'=>'w50')
);


// add field theme_plus_javascripts
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascripts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts'],
	'inputType'               => 'fancyCheckboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getJavaScripts'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);


// add empty option to mootools source
$GLOBALS['TL_DCA']['tl_layout']['fields']['mooSource']['eval']['includeBlankOption'] = true;


// add field theme_plus_exclude_files
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


/**
 * Class tl_layout_theme_plus
 */
class tl_layout_theme_plus extends Backend
{
	public function onload($dc)
	{
		if ($dc) {
			$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE id=?")->execute($dc->id);
			if ($objLayout->next() && $objLayout->theme_plus_exclude_frameworkcss)
			{
				// remove framework css related fields
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
	
	
	public function getFiles($strTypePrefix, $arrInheritedFiles = false)
	{
		$arrFiles = array();
		
		$objTheme = $this->Database
			->execute("SELECT * FROM tl_theme ORDER BY name");
		while ($objTheme->next())
		{
			$objFile = $this->Database
				->prepare("SELECT * FROM tl_theme_plus_file WHERE pid=? AND (type=? OR type=?) ORDER BY {$strTypePrefix}_file, {$strTypePrefix}_url")
				->execute($objTheme->id, $strTypePrefix . '_file', $strTypePrefix . '_url');
			while ($objFile->next())
			{
				$strType = $objFile->type;
				$label = $objFile->$strType;

				switch ($objFile->type) {
				case 'js_file': case 'js_url':
					$image = 'iconJS.gif';
					$label = '[' . $objTheme->position . '] ' . $label;
					break;

				case 'css_file': case 'css_url':
					$image = 'iconCSS.gif';
					break;

				default:
					$image = false;
				}

				$arrFile = array
				(
					'value' => $objFile->id,
					'label' => ($image ? $this->generateImage($image, $label, 'style="vertical-align:middle"') . ' ' : '') . '[' . $objTheme->name . '] ' . $label
				);
				
				if ($arrInheritedFiles && in_array($objFile->id, $arrInheritedFiles))
				{
					$arrFile['checked']  = true;
					$arrFile['disabled'] = true;
				}
				
				$arrFiles[] = $arrFile;
			}
		}
		return $arrFiles;
	}
}
