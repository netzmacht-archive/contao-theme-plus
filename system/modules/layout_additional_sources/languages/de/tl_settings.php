<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Layout Additional Sources
 * Copyright (C) 2011 Tristan Lins
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
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    LGPL
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_combination']              = array('Vereinigung', 'Wählen Sie hier ob und welche Dateien vereinigt werden sollen.');
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_css_compression']          = array('CSS Komprimierung', 'Wählen Sie hier wie CSS Code komprimiert werden soll.');
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_js_compression']           = array('JavaScript Komprimierung', 'Wählen Sie hier wie JavaScript Code komprimiert werden soll.');
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_gz_compression_disabled']  = array('GZip Komprimierung deaktivieren', 'GZip Komprimierung für CSS und JS Dateien deaktivieren.');
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_hide_cssmin_message']      = array('cssMinimizer Meldung abschalten', 'Die Info-Meldung zum cssMinimizer auf der Startseite abschalten.');
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_hide_jsmin_message']       = array('jsMinimizer/D\'E\'Packer Meldung abschalten', 'Die Info-Meldung zum jsMinimizer/DeanEdwardsPacker auf der Startseite abschalten.');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_combinations']['combine_all']   = 'Alle (lokale und externe Quellen)';
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_combinations']['combine_local'] = 'Nur lokale Dateien (keine externe Quellen)';
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_combinations']['combine_none']  = 'Dateien nicht vereinigen';
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['inherit']        = 'Compression API Voreinstellung';
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['less.js']        = 'less.js';
$GLOBALS['TL_LANG']['tl_settings']['additional_sources_compression']['less.js+pre']    = 'less.js (Vorkompiliert)';


/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_settings']['additional_source_legend'] = 'Zusätzliche Layoutdateien Einstellungen';

?>
