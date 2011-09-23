<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright

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
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_files'] = 'theme_plus_stylesheets,theme_plus_javascripts';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_files_noinherit'] = 'theme_plus_stylesheets_noinherit,theme_plus_javascripts_noinherit';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_files'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_files'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets'],
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getStylesheets'),
	'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts'],
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getJavaScripts'),
	'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_files_noinherit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_files_noinherit'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'long')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets_noinherit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets_noinherit'],
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getStylesheets'),
	'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'clr')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts_noinherit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts_noinherit'],
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_layout_theme_plus', 'getJavaScripts'),
	'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'clr')
);
