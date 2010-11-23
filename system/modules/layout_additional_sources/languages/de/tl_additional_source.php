<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_additional_source']['type']               = array('Dateityp', 'Bitte wählen Sie hier den Typ der Datei.');
$GLOBALS['TL_LANG']['tl_additional_source']['js_file']            = array('JavaScript Datei', 'Bitte wählen Sie hier die JavaScript Datei aus.');
$GLOBALS['TL_LANG']['tl_additional_source']['js_url']             = array('JavaScript URL', 'Bitte geben Sie die URL zur JavaScript Datei an.');
$GLOBALS['TL_LANG']['tl_additional_source']['css_file']           = array('CSS Datei', 'Bitte wählen Sie hier die CSS Datei aus.');
$GLOBALS['TL_LANG']['tl_additional_source']['css_url']            = array('CSS URL', 'Bitte geben Sie die URL zur CSS Datei an.');
$GLOBALS['TL_LANG']['tl_additional_source']['cc']                 = array('Conditional Comment', 'Conditional Comments ermöglichen das Einbinden Internet Explorer-spezifischer Dateien.');
$GLOBALS['TL_LANG']['tl_additional_source']['media']              = array('Medientypen', 'Bitte wählen Sie die Medientypen aus, für die die CSS Datei gültig ist.');
$GLOBALS['TL_LANG']['tl_additional_source']['restrictLayout']     = array('Datei auf Seitenlayout beschränken', 'Bitte wählen Sie hier die Seitenlayout aus, in denen die Datei eingebunden wird.');
$GLOBALS['TL_LANG']['tl_additional_source']['layout']             = array('Seitenlayout', 'Seitenlayouts können mit dem Modul "Themes" verwaltet werden.');
$GLOBALS['TL_LANG']['tl_additional_source']['compress_yui']       = array('YUI Komprimieren', 'Datei mit dem YUI Compressor komprimieren. (siehe WIKI Details bei Problemen mit der Einrichtung)');
$GLOBALS['TL_LANG']['tl_additional_source']['compress_gz']        = array('GZ Komprimieren', 'Datei mit GZ komprimieren.');
$GLOBALS['TL_LANG']['tl_additional_source']['compress_outdir']    = array('Ausgabeverzeichnis', 'Hier kann ein Ausgabeverzeichnis definiert werden, wenn die komprimierte Datei nicht im gleichen Verzeichnis wie das Original abgelegt werden soll.');
$GLOBALS['TL_LANG']['tl_additional_source']['editor_integration'] = array('WYSIWYG Editor Integration', 'Hier kann die Datei dem WYSIWYG Editor hinzugefügt werden. Lesen Sie hierzu die Hinweise im Handbuch.');
$GLOBALS['TL_LANG']['tl_additional_source']['editor_only']        = array('Nur im Editor benutzen', 'Hier können Sie die Datei als reine Editor-Datei definieren, sie wird dann nicht in der Seite selbst eingebunden.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_additional_source']['source_legend']         = 'Datei';
$GLOBALS['TL_LANG']['tl_additional_source']['editor_legend']         = 'WYSIWYG Editor Integration';
$GLOBALS['TL_LANG']['tl_additional_source']['restrict_legend']       = 'Layoutbeschränkung';
$GLOBALS['TL_LANG']['tl_additional_source']['compress_legend']       = 'Komprimierung';
$GLOBALS['TL_LANG']['tl_additional_source']['editors']['default']    = 'Standard Editor';
$GLOBALS['TL_LANG']['tl_additional_source']['editors']['newsletter'] = 'Newsletter Editor';
$GLOBALS['TL_LANG']['tl_additional_source']['editors']['flash']      = 'Flash Editor';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_additional_source']['new']         = array('Neue Datei', 'Eine neue Datei anlegen');
$GLOBALS['TL_LANG']['tl_additional_source']['show']        = array('Details', 'Details der Datei ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_additional_source']['edit']        = array('Datei bearbeiten', 'Datei ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_additional_source']['delete']      = array('Datei löschen', 'Datei ID %s löschen');
$GLOBALS['TL_LANG']['tl_additional_source']['cut']         = array('Datei verschieben ', 'Datei ID %s verschieben');
$GLOBALS['TL_LANG']['tl_additional_source']['copy']        = array('Datei duplizieren', 'Datei ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_additional_source']['delete']      = array('Datei löschen', 'Datei ID %s löschen');

?>