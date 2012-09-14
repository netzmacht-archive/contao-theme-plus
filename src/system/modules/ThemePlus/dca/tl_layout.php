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
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = preg_replace(
	array(
		'#external#',
		'#analytics#',
		// '#(\{expert_legend:hide\}.*);#U'
	),
	array(
		'external,theme_plus_stylesheets',
		'theme_plus_javascripts,theme_plus_javascript_lazy_load,theme_plus_default_javascript_position,analytics',
		// '$1,theme_plus_exclude_files;'
	),
	$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);


/**
 * Fields
 */
// add field theme_plus_stylesheets
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_stylesheets'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('ThemePlus\DataContainer\Layout', 'getStylesheets'),
	'eval'                    => array('multiple'=> true,
	                                   'tl_class'=> 'clr'),
    'sql' => 'blob NULL',
);

// add field theme_plus_javascripts
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascripts'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options_callback'        => array('ThemePlus\DataContainer\Layout', 'getJavaScripts'),
	'eval'                    => array('multiple'=> true,
	                                   'tl_class'=> 'clr'),
    'sql' => 'blob NULL',
);

// add field theme_plus_javascript_lazy_load
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascript_lazy_load'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascript_lazy_load'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=> 'w50 m12'),
    'sql' => "char(1) NOT NULL default ''"
);

// add field theme_plus_default_files_position
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_default_javascript_position'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_default_javascript_position'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('head', 'head+body', 'body'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_layout']['positions'],
	'eval'                    => array('tl_class'=> 'w50'),
    'sql' => "char(9) NOT NULL default 'head'"
);

// add field theme_plus_exclude_files
/*
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_files'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files'],
	'exclude'                 => true,
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
	),
    'sql' => 'blob NULL',
);
*/

// add css class clr to analytics field
$GLOBALS['TL_DCA']['tl_layout']['fields']['analytics']['eval']['tl_class'] .= ' clr';
