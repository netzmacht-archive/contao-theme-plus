<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_module']['palettes']['script_source'] = '{title_legend},name,type;{script_source_legend},script_source';
$GLOBALS['TL_DCA']['tl_module']['fields']['script_source'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['script_source'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_theme_plus', 'getJavaScriptFiles'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
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
		if (!$objTheme->next())
		{
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
			$label = ' ' . $objJavaScriptFiles->$strType;
			
			$arrJavaScriptFiles[$objJavaScriptFiles->id] = $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label;
		}
		return $arrJavaScriptFiles;
	}
}
?>