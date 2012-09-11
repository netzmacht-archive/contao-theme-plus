<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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


$this->loadDataContainer('tl_layout');

/**
 * Palettes
 */
foreach (array('regular', 'forward', 'redirect', 'root') as $strType)
{
	$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_files';
	$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_files_noinherit';

	$GLOBALS['TL_DCA']['tl_page']['palettes'][$strType] = preg_replace(
		'#({layout_legend:hide}.*);#U',
		'$1,theme_plus_include_files,theme_plus_include_files_noinherit;',
		$GLOBALS['TL_DCA']['tl_page']['palettes'][$strType]);
}
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_files']           = 'theme_plus_stylesheets,theme_plus_javascripts';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_files_noinherit'] = 'theme_plus_stylesheets_noinherit,theme_plus_javascripts_noinherit';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_files']           = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_files'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=> true,
	                                   'tl_class'      => 'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets']             = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_page_theme_plus', 'getStylesheets'),
	'eval'                    => array('checked_options_callback' => array('tl_page_theme_plus', 'getStylesheetsCheckedState'),
	                                   'disabled_options_callback'=> array('tl_page_theme_plus', 'getStylesheetsDisabledState'),
	                                   'mixin_value_callback'     => array('tl_page_theme_plus', 'inheritStylesheets'),
	                                   'multiple'                 => true,
	                                   'tl_class'                 => 'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts']             = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_page_theme_plus', 'getJavaScripts'),
	'eval'                    => array('checked_options_callback' => array('tl_page_theme_plus', 'getJavaScriptsCheckedState'),
	                                   'disabled_options_callback'=> array('tl_page_theme_plus', 'getJavaScriptsDisabledState'),
	                                   'mixin_value_callback'     => array('tl_page_theme_plus', 'inheritJavaScripts'),
	                                   'multiple'                 => true,
	                                   'tl_class'                 => 'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_files_noinherit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_files_noinherit'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=> true,
	                                   'tl_class'      => 'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets_noinherit']   = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets_noinherit'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_page_theme_plus', 'getStylesheets'),
	'eval'                    => array('checked_options_callback' => array('tl_page_theme_plus', 'getStylesheetsCheckedState'),
	                                   'disabled_options_callback'=> array('tl_page_theme_plus', 'getStylesheetsDisabledState'),
	                                   'mixin_value_callback'     => array('tl_page_theme_plus', 'inheritStylesheets'),
	                                   'multiple'                 => true,
	                                   'tl_class'                 => 'clr')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts_noinherit']   = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts_noinherit'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_page_theme_plus', 'getJavaScripts'),
	'eval'                    => array('checked_options_callback' => array('tl_page_theme_plus', 'getJavaScriptsCheckedState'),
	                                   'disabled_options_callback'=> array('tl_page_theme_plus', 'getJavaScriptsDisabledState'),
	                                   'mixin_value_callback'     => array('tl_page_theme_plus', 'inheritJavaScripts'),
	                                   'multiple'                 => true,
	                                   'tl_class'                 => 'clr')
);


/**
 * Class tl_page_theme_plus
 *
 */
class tl_page_theme_plus extends tl_layout_theme_plus
{
	public function getStylesheetsCheckedState()
	{
		return $this->getCheckedState('css', $this->inheritStylesheets($this->Input->get('id')));
	}

	public function getStylesheetsDisabledState()
	{
		return $this->getDisabledState('css', $this->inheritStylesheets($this->Input->get('id')));
	}

	public function getJavaScriptsCheckedState()
	{
		return $this->getCheckedState('js', $this->inheritJavaScripts($this->Input->get('id')));
	}

	public function getJavaScriptsDisabledState()
	{
		return $this->getDisabledState('js', $this->inheritJavaScripts($this->Input->get('id')));
	}

	public function inheritStylesheets()
	{
		return $this->inheritFiles('stylesheets');
	}

	public function inheritJavaScripts()
	{
		return $this->inheritFiles('javascripts');
	}

	public function inheritFiles($strType)
	{
		$arrFiles = array();

		$objPage = $this->getPageDetails($this->Input->get('id'));

		$objLayout = $this->Database
			->prepare("SELECT * FROM tl_layout WHERE id=?")
			->execute($objPage->layout);
		if ($objLayout->next()) {
			$key      = 'theme_plus_' . $strType;
			$arrFiles = array_merge(
				$arrFiles,
				deserialize($objLayout->$key, true)
			);
		}

		while ($objPage->pid > 0)
		{
			$objPage = $this->Database
				->prepare("SELECT *
						   FROM tl_page
						   WHERE id=?")
				->execute($objPage->pid);
			if ($objPage->next() && $objPage->theme_plus_include_files) {
				$key      = 'theme_plus_' . $strType;
				$arrFiles = array_merge(
					$arrFiles,
					deserialize($objPage->$key, true)
				);
			}
		}

		return $arrFiles;
	}

	public function getCheckedState($strTypePrefix, array $arrInheritedFiles = array())
	{
		$arrChecked = array();

		$objTheme = $this->Database
			->execute("SELECT *
					   FROM tl_theme
					   ORDER BY name");
		while ($objTheme->next())
		{
			$objFile = $this->Database
				->prepare("SELECT *
						   FROM tl_theme_plus_file
						   WHERE pid=?
						   AND (type=? OR type=?)
						   ORDER BY {$strTypePrefix}_file, {$strTypePrefix}_url")
				->execute($objTheme->id, $strTypePrefix . '_file', $strTypePrefix . '_url');
			while ($objFile->next())
			{
				if (in_array($objFile->id, $arrInheritedFiles)) {
					$arrChecked[] = $objFile->id;
				}
			}
		}
		return $arrChecked;
	}

	public function getDisabledState($strTypePrefix, array $arrInheritedFiles = array())
	{
		$arrDisabled = array();

		$objTheme = $this->Database
			->execute("SELECT *
					   FROM tl_theme
					   ORDER BY name");
		while ($objTheme->next())
		{
			$objFile = $this->Database
				->prepare("SELECT *
						   FROM tl_theme_plus_file
						   WHERE pid=?
						   AND (type=? OR type=?)
						   ORDER BY {$strTypePrefix}_file, {$strTypePrefix}_url")
				->execute($objTheme->id, $strTypePrefix . '_file', $strTypePrefix . '_url');
			while ($objFile->next())
			{
				if (in_array($objFile->id, $arrInheritedFiles)) {
					$arrDisabled[] = $objFile->id;
				}
			}
		}
		return $arrDisabled;
	}
}
