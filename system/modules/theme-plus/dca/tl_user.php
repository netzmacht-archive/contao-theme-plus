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
 * Table tl_user
 */
MetaPalettes::appendAfter('tl_user', 'login', 'theme', ['frontend' => [':hide', 'themePlusDesignerMode']]);
MetaPalettes::appendAfter('tl_user', 'admin', 'theme', ['frontend' => [':hide', 'themePlusDesignerMode']]);
MetaPalettes::appendAfter('tl_user', 'default', 'theme', ['frontend' => [':hide', 'themePlusDesignerMode']]);
MetaPalettes::appendAfter('tl_user', 'group', 'theme', ['frontend' => [':hide', 'themePlusDesignerMode']]);
MetaPalettes::appendAfter('tl_user', 'extend', 'theme', ['frontend' => [':hide', 'themePlusDesignerMode']]);
MetaPalettes::appendAfter('tl_user', 'custom', 'theme', ['frontend' => [':hide', 'themePlusDesignerMode']]);

$GLOBALS['TL_DCA']['tl_user']['fields']['themePlusDesignerMode'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['themePlusDesignerMode'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'sql'       => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_stylesheet';
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_javascript';
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_variable';
