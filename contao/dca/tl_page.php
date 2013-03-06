<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


$this->loadDataContainer('tl_layout');

/**
 * Palettes
 */
foreach (array('regular', 'forward', 'redirect', 'root') as $strType) {
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_stylesheets';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_javascripts';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_stylesheets_noinherit';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'theme_plus_include_javascripts_noinherit';

    $GLOBALS['TL_DCA']['tl_page']['palettes'][$strType] = preg_replace(
        '#({layout_legend:hide}.*);#U',
        '$1,theme_plus_include_stylesheets,theme_plus_include_stylesheets_noinherit,theme_plus_include_javascripts,theme_plus_include_javascripts_noinherit;',
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$strType]
    );
}
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_stylesheets']           = 'theme_plus_stylesheets';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_javascripts']           = 'theme_plus_javascripts';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_stylesheets_noinherit'] = 'theme_plus_stylesheets_noinherit';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['theme_plus_include_javascripts_noinherit'] = 'theme_plus_javascripts_noinherit';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_stylesheets']           = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_stylesheets'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ),
    'sql'       => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets']                   = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => array('ThemePlus\DataContainer\Page', 'getStylesheets'),
    'eval'             => array(
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'long'
    ),
    'sql'              => 'blob NULL'
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_javascripts']           = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_javascripts'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ),
    'sql'       => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts']                   = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => array('ThemePlus\DataContainer\Page', 'getJavaScripts'),
    'eval'             => array(
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'long'
    ),
    'sql'              => 'blob NULL'
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_stylesheets_noinherit'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_stylesheets_noinherit'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ),
    'sql'       => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_stylesheets_noinherit']         = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_stylesheets_noinherit'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => array('ThemePlus\DataContainer\Page', 'getStylesheets'),
    'eval'             => array(
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'clr'
    ),
    'sql'              => 'blob NULL'
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_include_javascripts_noinherit'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_include_javascripts_noinherit'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array(
        'submitOnChange' => true,
        'tl_class'       => 'long clr'
    ),
    'sql'       => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_page']['fields']['theme_plus_javascripts_noinherit']         = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_page']['theme_plus_javascripts_noinherit'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'options_callback' => array('ThemePlus\DataContainer\Page', 'getJavaScripts'),
    'eval'             => array(
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'clr'
    ),
    'sql'              => 'blob NULL'
);
