<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(
	'stylesheet',
	'theme_plus_exclude_contaocss,theme_plus_exclude_frameworkcss,stylesheet,theme_plus_files',
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

$GLOBALS['TL_DCA']['tl_layout']['fields']['stylesheets']['eval']['tl_class'] .= ' clr';

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_files'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_files'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_layout_theme_plus', 'getFiles'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['mooSource']['eval']['includeBlankOption'] = true;

/**
 * Class tl_layout_theme_plus
 *
 */
class tl_layout_theme_plus extends Backend
{
	public function getFiles()
	{
		$arrFile = array();
		$objFile = $this->Database->prepare("
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
				ORDER BY
					s.sorting")
		   ->execute($this->Input->get('id'));
		while ($objFile->next())
		{
			$strType = $objFile->type;
			$label = $objFile->$strType;

			if (strlen($objFile->media)) {
				$label .= ' <span style="color: #B3B3B3;">[' . $objFile->media . ']</span>';
			}

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

			$arrFile[$objFile->id] = ($image ? $this->generateImage($image, $label, 'style="vertical-align:middle"') . ' ' : '') . $label;
		}
		return $arrFile;
	}
}
?>