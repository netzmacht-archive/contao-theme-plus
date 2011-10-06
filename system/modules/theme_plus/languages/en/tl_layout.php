<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Theme+
 * Copyright (C) 2010,2011 InfinitySoft <http://www.infinitysoft.de>
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 * @license    LGPL
 */


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_contaocss']    = array('Remove Contao Core CSS', 'Remove the Contao Core CSS (contao.css) from this layout.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_frameworkcss'] = array('Do not include Contao Framework CSS', 'Deactivate the Contao Framework CSS code.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets']          = array('Additional stylesheets', 'Choose additional stylesheets to add to this layout.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts']          = array('Additional javascripts', 'Choose additional javascripts to add to this layout.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files']        = array('Filter files', 'Files, e.g. from plugins can be deactivated with this feature. Enter each file path to a single textfield. On global level you can also do this thrue the array <code>$GLOBALS[\'TL_THEME_EXCLUDE\']</code>, e.g. <code>$GLOBALS[\'TL_THEME_EXCLUDE\'][] = \'system/contao.css\';</code>.', '');

?>