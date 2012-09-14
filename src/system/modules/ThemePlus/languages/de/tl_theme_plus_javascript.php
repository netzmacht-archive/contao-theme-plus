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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['type']                     = array('Dateityp',
                                                                                    'Bitte wählen Sie hier den Typ der Datei.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['code_snippet_title']       = array('Code-Snippet Title',
                                                                                    'Bitte geben Sie einen Titel zu dem Code-Snippet ein.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['file']                     = array('Datei',
                                                                                    'Bitte wählen Sie hier die JavaScript Datei aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['url']                      = array('URL',
                                                                                    'Bitte geben Sie die URL zur JavaScript Datei an.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['code']                     = array('Code-Snippet',
                                                                                    'Bitte geben Sie hier Ihr JavaScript Code-Snippet ein.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['position']                 = array('Positionierung im HTML',
                                                                                    'Bitte wählen Sie hier, ob das JavaScript im &lt;head&gt; oder am Ende des &lt;body&gt; eingebunden werden soll.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['aggregation']              = array('Zusammenfassung',
                                                                                    'Bitte wählen Sie hier, wie diese Datei mit anderen zusammengefasst werden darf.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['media']                    = array('Medientypen',
                                                                                    'Bitte wählen Sie die Medientypen aus, für die die CSS Datei gültig ist.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['cc']                       = array('Conditional Comment',
                                                                                    'Conditional Comments ermöglichen das Einbinden Internet Explorer-spezifischer Dateien. Hier muss nur die Bedingung z.B. "lte IE 7" oder "IE 9" eingefügt werden. Der Prefix "&lt;!--[if" bzw. Sufix "]&gt;" werden automatisch eingefügt.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['frameworkContext']         = array('Framework',
                                                                                    'Wählen Sie hier, in welchem Framework Context das Script ausgeführt werden soll.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['filter']                   = array('Filter anwenden',
                                                                                    'Wählen Sie diese Option, können Sie verschiedene Serverseitige Filter anwenden. (Achtung: Dieses Feature ist inkompatibel zum Seitencache! Eine entsprechende Änderung an Contao wurde bereits beantragt.)');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['filterRule']               = array('Filter',
                                                                                    'Wählen Sie hier die Serverseitigen Filter aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['filterInvert']             = array('Filter umkehren',
                                                                                    'Hiermit können Sie die Filterlogik umkehren.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['editor_integration']       = array('WYSIWYG Editor Integration',
                                                                                    'Hier kann die Datei dem WYSIWYG Editor hinzugefügt werden. Lesen Sie hierzu die Hinweise im Handbuch.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['force_editor_integration'] = array('WYSIWYG Editor Integration erzwingen',
                                                                                    'Datei dem WYSIWYG Editor hinzugefügt auch wenn diese nicht dem Layout zugewiesen wurde.');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['asseticFilter']            = array('Assetic Filter',
                                                                                    'Wählen Sie hier einen Assetic Filter oder eine Filter Chain aus, die auf diese Datei angewendet werden.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['source_legend']          = 'Dateityp';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['file_legend']            = 'Dateiquelle';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['filter_legend']          = 'Filter-Einstellungen';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['editor_legend']          = 'WYSIWYG Editor Integration';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['assetic_legend']         = 'Assetic Einstellungen';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['expert_legend']          = 'Experten-Einstellungen';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['editors']['default']     = 'Standard Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['editors']['newsletter']  = 'Newsletter Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['editors']['flash']       = 'Flash Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['positions']['head']      = 'im &lt;head&gt;';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['positions']['body']      = 'am Ende des &lt;body&gt;';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['aggregations']['global'] = 'Mit allen anderen Dateien';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['aggregations']['theme']  = 'Mit allen Dateien aus dem gleichem Theme';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['aggregations']['pages']  = 'Mit allen Dateien aus der Seite und den ererbten Dateien der Elternseiten';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['aggregations']['page']   = 'Mit allen Dateien aus der Seite ohne ererbte Dateien';
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['aggregations']['never']  = 'Niemals';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['new']        = array('Neues JavaScript',
                                                                      'Eine neues JavaScript einbinden');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['newUrl']     = array('URL', 'Eine neue JavaScript URL anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['newFile']    = array('Datei', 'Eine neue JavaScript Datei anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['newCode']    = array('Code',
                                                                      'Eine neue JavaScript Code-Snippet anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['show']       = array('Details', 'Details der Datei ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['edit']       = array('Datei bearbeiten', 'Datei ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['delete']     = array('Datei löschen', 'Datei ID %s löschen');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['cut']        = array('Datei verschieben', 'Datei ID %s verschieben');
$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['copy']       = array('Datei duplizieren', 'Datei ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['pasteafter'] = array('Oben einfügen', 'Nach der Datei ID %s einfügen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['pastenew']   = array('Neue Datei oben erstellen',
                                                                      'Neues Element nach der Datei ID %s erstellen');
