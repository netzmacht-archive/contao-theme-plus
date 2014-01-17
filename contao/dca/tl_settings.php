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


/**
 * Palettes
 */
\MetaPalettes::appendTo(
    'tl_settings',
    array(
        'theme_plus' => array(
            'theme_plus_compile_mode'
        )
    )
);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_compile_mode']           = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_compile_mode'],
    'inputType' => 'select',
    'options'   => array('immediate', 'pre-compiled'),
    'reference'     => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_compile_modes'],
    'eval'      => array(
        'tl_class' => 'w50'
    ),
);
