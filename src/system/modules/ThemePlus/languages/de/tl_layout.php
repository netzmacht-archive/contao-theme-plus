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
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_contaocss']           = array('Contao Core CSS nicht einbinden',
                                                                                   'Deaktiviert die Contao Core CSS (contao.css).');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_frameworkcss']        = array('Contao Framework CSS nicht einbinden',
                                                                                   'Deaktiviert den Contao Framework CSS Code.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_stylesheets']                 = array('Weitere Stylesheets',
                                                                                   'Weitere Stylesheets in das Layout einbinden');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascript_lazy_load']        = array('LazyLoad JavaScript',
                                                                                   '<a href="http://friendlybit.com/js/lazy-loading-asyncronous-javascript/" onclick="window.open(this.href); return false;">Lazy Loading Asynchronous Javascript</a> - Wählen Sie diese Option, wird JavaScript später geladen.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_default_javascript_position'] = array('Standardpositionierung von JavaScript Dateien',
                                                                                   'Wählen Sie hier aus wo die JavaScript Dateien aus dem JavaScript Framework und aus TL_JAVASCRIPT positioniert werden sollen.');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_javascripts']                 = array('Weitere JavaScripts',
                                                                                   'Weitere JavaScripts in das Layout einbinden');
$GLOBALS['TL_LANG']['tl_layout']['theme_plus_exclude_files']               = array('Stylesheets und JavaScripts filtern',
                                                                                   'Stylesheets und JavaScripts bpsw. aus Plugins können mit diesem Feature deaktiviert werden. Geben Sie dazu jeden Dateipfad einzeln in ein Feld ein. Auf globaler Ebene kann dies auch über das Array <code>$GLOBALS[\'TL_THEME_EXCLUDE\']</code> erfolgen, z.B. <code>$GLOBALS[\'TL_THEME_EXCLUDE\'][] = \'system/contao.css\';</code>.',
                                                                                   '');
$GLOBALS['TL_LANG']['tl_layout']['asseticStylesheetFilter']                = array('Assetic Filter für Stylesheets',
                                                                                   'Wählen Sie hier einen Assetic Filter oder eine Filter Chain aus, der abschließend auf <strong>alle</strong> Stylesheets angewendet wird.');
$GLOBALS['TL_LANG']['tl_layout']['asseticJavaScriptFilter']                = array('Assetic Filter für JavaScripts',
                                                                                   'Wählen Sie hier einen Assetic Filter oder eine Filter Chain aus, der abschließend auf <strong>alle</strong> JavaScripts angewendet wird.');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_layout']['positions']['head']      = 'Alles im &lt;head&gt;';
$GLOBALS['TL_LANG']['tl_layout']['positions']['head+body'] = 'Framework im &lt;head&gt;, weitere Dateien im &lt;body&gt;';
$GLOBALS['TL_LANG']['tl_layout']['positions']['body']      = 'Alles im &lt;body&gt;';
