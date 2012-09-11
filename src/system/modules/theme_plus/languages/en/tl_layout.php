<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_contaocss']    = array('Remove Contao Core CSS', 'Remove the Contao Core CSS (contao.css) from this layout.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_frameworkcss'] = array('Do not include Contao Framework CSS', 'Deactivate the Contao Framework CSS code.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets']          = array('Additional stylesheets', 'Choose additional stylesheets to add to this layout.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts']          = array('Additional javascripts', 'Choose additional javascripts to add to this layout.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files']        = array('Filter stylesheets und javasScripts', 'Stylesheets und javasScripts e.g. from plugins can be deactivated with this feature. Enter each file path to a single textfield. On global level you can also do this thrue the array <code>$GLOBALS[\'TL_THEME_EXCLUDE\']</code>, e.g. <code>$GLOBALS[\'TL_THEME_EXCLUDE\'][] = \'system/contao.css\';</code>.', '');
