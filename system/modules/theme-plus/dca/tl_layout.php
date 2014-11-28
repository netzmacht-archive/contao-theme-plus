<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = preg_replace(
    [
        '#external#',
        '#analytics#',
        '#(\{expert_legend:hide\}.*);#U',
    ],
    [
        'external,theme_plus_stylesheets',
        'theme_plus_javascripts,theme_plus_javascript_lazy_load,theme_plus_default_javascript_position,analytics',
        // '$1,theme_plus_exclude_files;',
        '$1,asseticStylesheetFilter,asseticJavaScriptFilter;'
    ],
    $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']
);


/**
 * Fields
 */
// add field theme_plus_stylesheets
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_stylesheets'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Layout', 'getStylesheets'],
    'eval'             => [
        'multiple' => true,
        'tl_class' => 'clr'
    ],
    'sql'              => 'blob NULL',
];

// add field theme_plus_javascripts
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascripts'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Layout', 'getJavaScripts'],
    'eval'             => [
        'multiple' => true,
        'tl_class' => 'clr'
    ],
    'sql'              => 'blob NULL',
];

// add field theme_plus_javascript_lazy_load
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_javascript_lazy_load'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascript_lazy_load'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default ''"
];

// add field theme_plus_default_files_position
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_default_javascript_position'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_default_javascript_position'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => ['head', 'body'],
    'reference' => &$GLOBALS['TL_LANG']['tl_layout']['positions'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "char(9) NOT NULL default 'head'"
];

$GLOBALS['TL_DCA']['tl_layout']['fields']['asseticStylesheetFilter'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_layout']['asseticStylesheetFilter'],
    'inputType'        => 'select',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'getAsseticFilterOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['assetic'],
    'eval'             => [
        'includeBlankOption' => true,
        'tl_class'           => 'w50'
    ],
    'sql'              => "varbinary(32) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_layout']['fields']['asseticJavaScriptFilter'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_layout']['asseticJavaScriptFilter'],
    'inputType'        => 'select',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\JavaScript', 'getAsseticFilterOptions'],
    'reference'        => &$GLOBALS['TL_LANG']['assetic'],
    'eval'             => [
        'includeBlankOption' => true,
        'tl_class'           => 'w50'
    ],
    'sql'              => "varbinary(32) NOT NULL default ''"
];

// add field theme_plus_exclude_files
/*
$GLOBALS['TL_DCA']['tl_layout']['fields']['theme_plus_exclude_files'] = [
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files'],
	'exclude'                 => true,
	'inputType'               => 'multitextWizard',
	'eval'                    => [
		'tl_class' => 'clr',
		'style'    => 'width:100%;',
		'columns'  => [
			[
				'label'     => &$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files'][2],
				'width'     => '600px'
			]
		]
	],
    'sql' => 'blob NULL',
];
*/

// add css class clr to analytics field
$GLOBALS['TL_DCA']['tl_layout']['fields']['analytics']['eval']['tl_class'] .= ' clr';
