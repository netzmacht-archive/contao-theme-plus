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
 * Table tl_theme
 */
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_stylesheet';
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_javascript';


/**
 * Operations
 */
$intOffset = array_search(
                 'css',
                 array_keys(
                     $GLOBALS['TL_DCA']['tl_theme']['list']['operations']
                 )
             ) + 1;

$GLOBALS['TL_DCA']['tl_theme']['list']['operations'] = array_merge
(
    array_slice($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 0, $intOffset),
    [
        'theme_plus_stylesheet' => [
            'label'           => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_stylesheet'],
            'href'            => 'table=tl_theme_plus_stylesheet',
            'icon'            => 'assets/theme-plus/images/stylesheet.png',
            'button_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Theme', 'editStylesheet']
        ],
        'theme_plus_javascript' => [
            'label'           => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_javascript'],
            'href'            => 'table=tl_theme_plus_javascript',
            'icon'            => 'assets/theme-plus/images/javascript.png',
            'button_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Theme', 'editJavaScript']
        ],
    ],
    array_slice($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], $intOffset)
);
