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

if (TL_MODE == 'BE') {
    $this->loadDataContainer('tl_layout');
}

/**
 * Palettes
 */
foreach (['regular', 'forward', 'redirect', 'root'] as $strType) {
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_stylesheets';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_javascripts';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_stylesheets_noinherit';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_javascripts_noinherit';

    $GLOBALS['TL_DCA']['tl_page']['palettes'][$strType] = preg_replace(
        '#({layout_legend(:hide)?}.*);#U',
        '$1,theme_plus_include_stylesheets,theme_plus_include_stylesheets_noinherit,theme_plus_include_javascripts,theme_plus_include_javascripts_noinherit;',
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$strType]
    );
}
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_stylesheets']           = 'theme_plus_stylesheets';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_javascripts']           = 'theme_plus_javascripts';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_stylesheets_noinherit'] =
    'theme_plus_stylesheets_noinherit';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_javascripts_noinherit'] =
    'theme_plus_javascripts_noinherit';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_stylesheets']           = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_stylesheets'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ],
    'sql'       => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets']                   = [
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Page', 'getStylesheets'],
    'eval'             => [
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'long'
    ],
    'sql'              => 'blob NULL'
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_javascripts']           = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_javascripts'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ],
    'sql'       => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts']                   = [
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Page', 'getJavaScripts'],
    'eval'             => [
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'long'
    ],
    'sql'              => 'blob NULL'
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_stylesheets_noinherit'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_stylesheets_noinherit'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ],
    'sql'       => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets_noinherit']         = [
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets_noinherit'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Page', 'getStylesheets'],
    'eval'             => [
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'clr'
    ],
    'sql'              => 'blob NULL'
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_javascripts_noinherit'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_javascripts_noinherit'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ],
    'sql'       => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts_noinherit']         = [
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts_noinherit'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Page', 'getJavaScripts'],
    'eval'             => [
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'clr'
    ],
    'sql'              => 'blob NULL'
];
