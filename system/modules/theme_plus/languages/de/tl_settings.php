<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


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
