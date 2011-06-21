<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(
	'stylesheet',
	'stylesheet,theme_plus_files',
	$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_files'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_files'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_layout_theme_plus', 'getFiles'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

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