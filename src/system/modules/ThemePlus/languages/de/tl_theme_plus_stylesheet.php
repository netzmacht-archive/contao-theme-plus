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
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['type']                     = array('Dateityp',
                                                                                    'Bitte wählen Sie hier den Typ der Datei.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['code_snippet_title']       = array('Code-Snippet Title',
                                                                                    'Bitte geben Sie einen Titel zu dem Code-Snippet ein.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['file']                     = array('Datei',
                                                                                    'Bitte wählen Sie hier die CSS Datei aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['url']                      = array('URL',
                                                                                    'Bitte geben Sie die URL zur CSS Datei an.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['code']                     = array('Code-Snippet',
                                                                                    'Bitte geben Sie hier Ihr CSS Code-Snippet ein.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['position']                 = array('Positionierung im HTML',
                                                                                    'Bitte wählen Sie hier, ob das JavaScript im &lt;head&gt; oder am Ende des &lt;body&gt; eingebunden werden soll.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregation']              = array('Zusammenfassung',
                                                                                    'Bitte wählen Sie hier, wie diese Datei mit anderen zusammengefasst werden darf.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['media']                    = array('Medientypen',
                                                                                    'Bitte wählen Sie die Medientypen aus, für die die CSS Datei gültig ist.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['cc']                       = array('Conditional Comment',
                                                                                    'Conditional Comments ermöglichen das Einbinden Internet Explorer-spezifischer Dateien. Hier muss nur die Bedingung z.B. "lte IE 7" oder "IE 9" eingefügt werden. Der Prefix "&lt;!--[if" bzw. Sufix "]&gt;" werden automatisch eingefügt.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['frameworkContext']         = array('Framework',
                                                                                    'Wählen Sie hier, in welchem Framework Context das Script ausgeführt werden soll.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filter']                   = array('Filter anwenden',
                                                                                    'Wählen Sie diese Option, können Sie verschiedene Serverseitige Filter anwenden. (Achtung: Dieses Feature ist inkompatibel zum Seitencache! Eine entsprechende Änderung an Contao wurde bereits beantragt.)');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filterRule']               = array('Filter',
                                                                                    'Wählen Sie hier die Serverseitigen Filter aus.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filterInvert']             = array('Filter umkehren',
                                                                                    'Hiermit können Sie die Filterlogik umkehren.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editor_integration']       = array('WYSIWYG Editor Integration',
                                                                                    'Hier kann die Datei dem WYSIWYG Editor hinzugefügt werden. Lesen Sie hierzu die Hinweise im Handbuch.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['force_editor_integration'] = array('WYSIWYG Editor Integration erzwingen',
                                                                                    'Datei dem WYSIWYG Editor hinzugefügt auch wenn diese nicht dem Layout zugewiesen wurde.');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['asseticFilter']            = array('Assetic Filter',
                                                                                    'Wählen Sie hier einen Assetic Filter oder eine Filter Chain aus, die auf diese Datei angewendet werden.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['source_legend']          = 'Dateityp';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['file_legend']            = 'Dateiquelle';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filter_legend']          = 'Filter-Einstellungen';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editor_legend']          = 'WYSIWYG Editor Integration';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['assetic_legend']         = 'Assetic Einstellungen';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['expert_legend']          = 'Experten-Einstellungen';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editors']['default']     = 'Standard Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editors']['newsletter']  = 'Newsletter Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editors']['flash']       = 'Flash Editor';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['positions']['head']      = 'im &lt;head&gt;';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['positions']['body']      = 'am Ende des &lt;body&gt;';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregations']['global'] = 'Mit allen anderen Dateien';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregations']['theme']  = 'Mit allen Dateien aus dem gleichem Theme';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregations']['pages']  = 'Mit allen Dateien aus der Seite und den ererbten Dateien der Elternseiten';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregations']['page']   = 'Mit allen Dateien aus der Seite ohne ererbte Dateien';
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregations']['never']  = 'Niemals';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['new']         = array('Neues CSS', 'Eine neue CSS einbinden');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newUrl']      = array('URL', 'Eine neue CSS URL anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newFile']     = array('Datei', 'Eine neue CSS Datei anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newCode']     = array('Code', 'Eine neue CSS Code-Snippet anlegen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['collections'] = array('Kollektionen',
                                                                       'Kollektionen von CSS Dateien anlegen und verwalten');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['show']        = array('Details', 'Details der Datei ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['edit']        = array('Datei bearbeiten', 'Datei ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['delete']      = array('Datei löschen', 'Datei ID %s löschen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['cut']         = array('Datei verschieben', 'Datei ID %s verschieben');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['copy']        = array('Datei duplizieren', 'Datei ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['pasteafter']  = array('Oben einfügen',
                                                                       'Nach der Datei ID %s einfügen');
$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['pastenew']    = array('Neue Datei oben erstellen',
                                                                       'Neues Element nach der Datei ID %s erstellen');
