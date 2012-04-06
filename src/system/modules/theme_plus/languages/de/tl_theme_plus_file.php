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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_theme_plus_file']['type']                     = array('Dateityp', 'Bitte wählen Sie hier den Typ der Datei.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_file']                  = array('JavaScript Datei', 'Bitte wählen Sie hier die JavaScript Datei aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_url']                   = array('JavaScript URL', 'Bitte geben Sie die URL zur JavaScript Datei an.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_file']                 = array('CSS Datei', 'Bitte wählen Sie hier die CSS Datei aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_url']                  = array('CSS URL', 'Bitte geben Sie die URL zur CSS Datei an.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['position']                 = array('Positionierung im HTML', 'Bitte wählen Sie hier, ob das JavaScript im &lt;head&gt; oder am Ende des &lt;body&gt; eingebunden werden soll.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['aggregation']              = array('Zusammenfassung', 'Bitte wählen Sie hier, wie diese Datei mit anderen zusammengefasst werden darf.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['media']                    = array('Medientypen', 'Bitte wählen Sie die Medientypen aus, für die die CSS Datei gültig ist.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['cc']                       = array('Conditional Comment', 'Conditional Comments ermöglichen das Einbinden Internet Explorer-spezifischer Dateien. Hier muss nur die Bedingung z.B. "lte IE 7" oder "IE 9" eingefügt werden. Der Prefix "&lt;!--[if" bzw. Sufix "]&gt;" werden automatisch eingefügt.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['filter']                   = array('Filter anwenden', 'Wählen Sie diese Option, können Sie verschiedene Serverseitige Filter anwenden. (Achtung: Dieses Feature ist inkompatibel zum Seitencache! Eine entsprechende Änderung an Contao wurde bereits beantragt.)');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['filterRule']               = array('Filter', 'Wählen Sie hier die Serverseitigen Filter aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['filterInvert']             = array('Filter umkehren', 'Hiermit können Sie die Filterlogik umkehren.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editor_integration']       = array('WYSIWYG Editor Integration', 'Hier kann die Datei dem WYSIWYG Editor hinzugefügt werden. Lesen Sie hierzu die Hinweise im Handbuch.');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['force_editor_integration'] = array('WYSIWYG Editor Integration erzwingen', 'Datei dem WYSIWYG Editor hinzugefügt auch wenn diese nicht dem Layout zugewiesen wurde.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_theme_plus_file']['source_legend']          = 'Datei';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editor_legend']          = 'WYSIWYG Editor Integration';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors']['default']     = 'Standard Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors']['newsletter']  = 'Newsletter Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors']['flash']       = 'Flash Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['positions']['head']      = 'im &lt;head&gt;';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['positions']['body']      = 'am Ende des &lt;body&gt;';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['aggregations']['global'] = 'Mit allen anderen Dateien';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['aggregations']['theme']  = 'Mit allen Dateien aus dem gleichem Theme';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['aggregations']['pages']  = 'Mit allen Dateien aus der Seite und den ererbten Dateien der Elternseiten';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['aggregations']['page']   = 'Mit allen Dateien aus der Seite ohne ererbte Dateien';
$GLOBALS['TL_LANG']['tl_theme_plus_file']['aggregations']['never']  = 'Niemals';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_theme_plus_file']['new']         = array('Neue Datei', 'Eine neue Datei anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['newJsUrl']    = array('Neue URL', 'Eine neue JavaScript URL anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['newJsFile']   = array('Neue Datei', 'Eine neue JavaScript Datei anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['newCssUrl']   = array('Neue URL', 'Eine neue CSS URL anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['newCssFile']  = array('Neue Datei', 'Eine neue CSS Datei anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['show']        = array('Details', 'Details der Datei ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['edit']        = array('Datei bearbeiten', 'Datei ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['delete']      = array('Datei löschen', 'Datei ID %s löschen');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['cut']         = array('Datei verschieben', 'Datei ID %s verschieben');
$GLOBALS['TL_LANG']['tl_theme_plus_file']['copy']        = array('Datei duplizieren', 'Datei ID %s duplizieren');
