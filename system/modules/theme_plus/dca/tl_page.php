<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


foreach (array('regular', 'forward', 'redirect', 'root') as $strType)
{
	$GLOBALS['TL_DCA']['tl_page']['palettes'][$strType] = preg_replace(
		'#({layout_legend:hide}.*);#U',
		'$1,theme_plus_files;',
		$GLOBALS['TL_DCA']['tl_page']['palettes'][$strType]);
}
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_files'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_files'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_page_theme_plus', 'getFiles'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

/**
 * Class tl_page_additional_source
 *
 */
class tl_page_theme_plus extends Backend
{
	public function getFiles()
	{
		$objPage = $this->getPageDetails($this->Input->get('id'));
		if (!$objPage->layout)
		{
			$objLayout = $this->Database->execute("SELECT * FROM tl_layout WHERE fallback='1'");
			if ($objLayout->next())
			{
				$objPage->layout = $objLayout->id;
			}
			else 
			{
				return array();
			}
		}
		
		$arrFiles = array();
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
		   ->execute($objPage->layout);
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
			
			$arrFiles[$objFile->id] = ($image ? $this->generateImage($image, $label, 'style="vertical-align:middle"') . ' ' : '') . $label;
		}
		return $arrFiles;
	}
}
?>