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
 * Table tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['themes']['options'][] = 'theme_plus_stylesheet';
$GLOBALS['TL_DCA']['tl_user_group']['fields']['themes']['options'][] = 'theme_plus_javascript';
$GLOBALS['TL_DCA']['tl_user_group']['fields']['themes']['options'][] = 'theme_plus_variable';
