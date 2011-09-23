<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


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
	'eval'                    => array('tl_class'=>'w50 m12')
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
