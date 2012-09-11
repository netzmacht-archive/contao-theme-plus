<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2012 InfinitySoft <http://www.infinitysoft.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
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
	'exclude'                 => true,
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
