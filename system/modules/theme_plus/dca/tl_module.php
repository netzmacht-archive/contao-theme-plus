<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_module']['palettes']['script_source'] = '{title_legend},name,type;{script_source_legend},script_source';
$GLOBALS['TL_DCA']['tl_module']['fields']['script_source'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['script_source'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_module_additional_source', 'getAdditionSources'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

/**
 * Class tl_module_additional_source
 *
 */
class tl_module_additional_source extends Backend
{
	public function getAdditionSources(DataContainer $dc)
	{
		$objTheme = $this->Database->prepare("SELECT * FROM tl_theme WHERE id=?")->execute($dc->activeRecord->pid);
		if (!$objTheme->next())
		{
			return array();
		}
		
		$arrAdditionalSource = array();
		$objAdditionalSource = $this->Database->prepare("
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
		while ($objAdditionalSource->next())
		{
			$strType = $objAdditionalSource->type;
			$label = ' ' . $objAdditionalSource->$strType;
			
			if (strlen($objAdditionalSource->cc)) {
				$label .= ' <span style="color: #B3B3B3;">[' . $objAdditionalSource->cc . ']</span>';
			}
			
			if (strlen($objAdditionalSource->media)) {
				$arrMedia = unserialize($objAdditionalSource->media);
				if (count($arrMedia)) {
					$label .= ' <span style="color: #B3B3B3;">[' . implode(', ', $arrMedia) . ']</span>';
				}
			}
			
			$arrAdditionalSource[$objAdditionalSource->id] = $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label;
		}
		return $arrAdditionalSource;
	}
}
?>