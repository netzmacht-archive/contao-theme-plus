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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_theme_plus_file']['type']                     = array('Dateityp', 'Bitte wählen Sie hier den Typ der Datei.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_file']                  = array('JavaScript Datei', 'Bitte wählen Sie hier die JavaScript Datei aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_url']                   = array('JavaScript URL', 'Bitte geben Sie die URL zur JavaScript Datei an.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_url_real_path']         = array('JavaScript URL - lokaler Dateipfad', 'Hier kann zur JavaScript URL, ein lokaler Pfad zu der Datei angegeben werden. Dieser wird verwendet, wenn die Dateien zusammengeführt werden.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_file']                 = array('CSS Datei', 'Bitte wählen Sie hier die CSS Datei aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_url']                  = array('CSS URL', 'Bitte geben Sie die URL zur CSS Datei an.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_url_real_path']        = array('CSS URL - lokaler Dateipfad', 'Hier kann zur CSS URL, ein lokaler Pfad zu der Datei angegeben werden. Dieser wird verwendet, wenn die Dateien zusammengeführt werden.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['cc']                       = array('Conditional Comment', 'Conditional Comments ermöglichen das Einbinden Internet Explorer-spezifischer Dateien.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['media']                    = array('Medientypen', 'Bitte wählen Sie die Medientypen aus, für die die CSS Datei gültig ist.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['restrictLayout']           = array('Datei auf Seitenlayout beschränken', 'Bitte wählen Sie hier die Seitenlayout aus, in denen die Datei eingebunden wird.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['layout']                   = array('Seitenlayout', 'Seitenlayouts können mit dem Modul "Themes" verwaltet werden.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['compress_yui']             = array('YUI Komprimieren', 'Datei mit dem YUI Compressor komprimieren. (siehe WIKI Details bei Problemen mit der Einrichtung)');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['compress_gz']              = array('GZ Komprimieren', 'Datei mit GZ komprimieren.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['compress_outdir']          = array('Ausgabeverzeichnis', 'Hier kann ein Ausgabeverzeichnis definiert werden, wenn die komprimierte Datei nicht im gleichen Verzeichnis wie das Original abgelegt werden soll.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editor_integration']       = array('WYSIWYG Editor Integration', 'Hier kann die Datei dem WYSIWYG Editor hinzugefügt werden. Lesen Sie hierzu die Hinweise im Handbuch.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['force_editor_integration'] = array('WYSIWYG Editor Integration erzwingen', 'Datei dem WYSIWYG Editor hinzugefügt auch wenn diese nicht dem Layout zugewiesen wurde.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_theme_plus_file']['source_legend']         = 'Datei';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editor_legend']         = 'WYSIWYG Editor Integration';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['restrict_legend']       = 'Layoutbeschränkung';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['compress_legend']       = 'Komprimierung';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors']['default']    = 'Standard Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors']['newsletter'] = 'Newsletter Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors']['flash']      = 'Flash Editor';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_theme_plus_file']['new']    = array('Neue Datei', 'Eine neue Datei anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['show']   = array('Details', 'Details der Datei ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['edit']   = array('Datei bearbeiten', 'Datei ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['delete'] = array('Datei löschen', 'Datei ID %s löschen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['cut']    = array('Datei verschieben ', 'Datei ID %s verschieben');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['copy']   = array('Datei duplizieren', 'Datei ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['delete'] = array('Datei löschen', 'Datei ID %s löschen');
