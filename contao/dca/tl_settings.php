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
MetaPalettes::appendTo(
    'tl_settings',
    [
        'theme_plus' => ['theme_plus_disabled_advanced_asset_caching']
    ]
);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['theme_plus_disabled_advanced_asset_caching'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['theme_plus_disabled_advanced_asset_caching'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50'
    ]
];
