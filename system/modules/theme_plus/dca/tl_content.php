<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_content']['palettes']['script_source'] = '{type_legend},type;{script_source_legend},script_source';
$GLOBALS['TL_DCA']['tl_content']['fields']['script_source'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['script_source'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_content_additional_source', 'getAdditionSources'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

/**
 * Class tl_content_additional_source
 *
 */
class tl_content_additional_source extends Backend
{
	public function getAdditionSources(DataContainer $dc)
	{
		$objArticle = $this->Database->prepare("SELECT * FROM tl_article WHERE id=?")->execute($dc->activeRecord->pid);
		if (!$objArticle->next())
		{
			return array();
		}
		
		$objPage = $this->getPageDetails($objArticle->pid);
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
		
		$arrAdditionalSource = array();
		$objAdditionalSource = $this->Database->prepare("
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