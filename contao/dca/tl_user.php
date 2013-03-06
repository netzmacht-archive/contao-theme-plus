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
 * Table tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_stylesheet';
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_javascript';
$GLOBALS['TL_DCA']['tl_user']['fields']['themes']['options'][] = 'theme_plus_variable';
