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


$GLOBALS['TL_CSS']['theme_plus_be'] = 'system/modules/ThemePlus/assets/css/be.css';


/**
 * Table tl_theme_plus_stylesheet
 */
$GLOBALS['TL_DCA']['tl_theme_plus_stylesheet'] = array
(

    // Config
    'config'          => array
    (
        'dataContainer'    => 'Table',
        'ptable'           => 'tl_theme',
        'enableVersioning' => true,
        'sql'              => array
        (
            'keys' => array
            (
                'id'  => 'primary',
                'pid' => 'index'
            )
        ),
    ),

    // List
    'list'            => array
    (
        'sorting'           => array
        (
            'mode'                  => 4,
            'flag'                  => 11,
            'fields'                => array('sorting'),
            'panelLayout'           => 'filter;limit',
            'headerFields'          => array('name', 'author', 'tstamp'),
            'child_record_callback' => array('ThemePlus\DataContainer\File', 'listFile'),
            'child_record_class'    => 'no_padding'
        ),
        'global_operations' => array
        (
            'newFile'        => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newFile'],
                'href'       => 'act=paste&mode=create&type=file',
                'class'      => 'header_new_file',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'newUrl'         => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newUrl'],
                'href'       => 'act=paste&mode=create&type=url',
                'class'      => 'header_new_url',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'newCode'        => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newCode'],
                'href'       => 'act=paste&mode=create&type=code',
                'class'      => 'header_new_code',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'collections'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['collections'],
                'href'       => 'table=tl_theme_plus_stylesheet_collection',
                'class'      => 'header_collection',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'all'            => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations'        => array
        (
            'edit'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'copy'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['copy'],
                'href'  => 'act=paste&amp;mode=copy',
                'icon'  => 'copy.gif'
            ),
            'cut'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes'        => array
    (
        '__selector__' => array('type', 'filter')
    ),

    // MetaPalettes
    'metapalettes'    => array
    (
        'default'  => array
        (
            'source' => array('type')
        ),
        'file'     => array
        (
            'source'  => array('type'),
            'file'    => array('file'),
            'filter'  => array(':hide', 'media', 'cc', 'filter'),
            'editor'  => array(':hide', 'editor_integration', 'force_editor_integration'),
            'assetic' => array(':hide', 'asseticFilter'),
            'expert'  => array(':hide', 'aggregation')
        ),
        'url'      => array
        (
            'source'  => array('type'),
            'file'    => array('url', 'fetchUrl'),
            'filter'  => array(':hide', 'media', 'cc', 'filter'),
            'editor'  => array(':hide', 'editor_integration', 'force_editor_integration'),
            'assetic' => array(':hide', 'asseticFilter'),
            'expert'  => array(':hide')
        ),
        'code'     => array
        (
            'source'  => array('type', 'code_snippet_title'),
            'file'    => array('code'),
            'filter'  => array(':hide', 'media', 'cc', 'filter'),
            'editor'  => array(':hide', 'editor_integration', 'force_editor_integration'),
            'assetic' => array(':hide', 'asseticFilter'),
            'expert'  => array(':hide', 'aggregation')
        )
    ),

    // MetaSubpalettes
    'metasubpalettes' => array
    (
        'filter' => array('filterRule', 'filterInvert')
    ),

    // Fields
    'fields'          => array
    (
        'id'                                    => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'                                   => array
        (
            'foreignKey'              => 'tl_style_sheet.name',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=> 'belongsTo', 'load'=> 'lazy')
        ),
        'sorting'                               => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp'                                => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'type'                                  => array
        (
            'label'         => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['type'],
            'default'       => \Input::get('type')
                ? : \Session::getInstance()
                    ->get('THEME_PLUS_LAST_CSS_TYPE'),
            'inputType'     => 'select',
            'filter'        => true,
            'options'       => array('file', 'url', 'code'),
            'reference'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet'],
            'eval'          => array('includeBlankOption'=> true,
                                     'submitOnChange'    => true,
                                     'tl_class'          => 'w50'),
            'save_callback' => array(array('ThemePlus\DataContainer\Stylesheet', 'rememberType')),
            'sql'           => "varchar(32) NOT NULL default ''"
        ),
        'code_snippet_title'                    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['code_snippet_title'],
            'inputType' => 'text',
            'eval'      => array('mandatory'     => true,
                                 'unique'        => true,
                                 'maxlength'     => 255,
                                 'tl_class'      => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'file'                                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['file'],
            'inputType' => 'fileTree',
            'eval'      => array('mandatory' => true,
                                 'fieldType' => 'radio',
                                 'files'     => true,
                                 'extensions'=> 'css,less,scss,sass',
                                 'path'      => $GLOBALS['TL_CONFIG']['uploadPath']),
            'sql'       => "blob NULL"
        ),
        'url'                                   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['url'],
            'inputType' => 'text',
            'eval'      => array('mandatory'     => true,
                                 'decodeEntities'=> true,
                                 'tl_class'      => 'long'),
            'sql'       => "blob NULL"
        ),
        'fetchUrl'                                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['fetchUrl'],
            'inputType' => 'checkbox',
            'eval'      => array(),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'code'                                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['code'],
            'inputType' => 'textarea',
            'eval'      => array('mandatory' => true,
                                 'allowHtml' => true,
                                 'class'     => 'monospace',
                                 'rte'       => 'codeMirror|css',
                                 'helpwizard'=> true),
            'sql'       => "blob NULL"
        ),
        'aggregation'                           => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregation'],
            'default'   => 'global',
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('global', 'theme', 'pages', 'page', 'never'),
            'reference' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['aggregations'],
            'sql'       => "varchar(6) NOT NULL default 'global'"
        ),
        'media'                                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['media'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('tl_class'      => 'long',
                                 'decodeEntities'=> true),
            'sql'       => "blob NULL"
        ),
        'cc'                                    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['cc'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('tl_class'=> 'long'),
            'sql'       => "blob NULL"
        ),
        'filter'                                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filter'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange'=> true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'filterRule'                            => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filterRule'],
            'inputType' => 'checkbox',
            'options'   => array
            (
                'OS'      => array
                (
                    'os-win'        => 'Windows',
                    'os-win-ce'     => 'Windows CE / Phone',
                    'os-mac'        => 'Macintosh',
                    'os-unix'       => 'UNIX (Linux, FreeBSD, OpenBSD, NetBSD)',
                    'os-ios'        => 'iOS (iPad, iPhone, iPod)',
                    'os-android'    => 'Android',
                    'os-blackberry' => 'Blackberry',
                    'os-symbian'    => 'Symbian',
                    'os-webos'      => 'WebOS'
                ),
                'Browser' => array
                (
                    'browser-ie'           => 'InternetExplorer',
                    'browser-ie-6'         => 'InternetExplorer 6',
                    'browser-ie-7'         => 'InternetExplorer 7',
                    'browser-ie-8'         => 'InternetExplorer 8',
                    'browser-ie-9'         => 'InternetExplorer 9',
                    'browser-ie-10'        => 'InternetExplorer 10',
                    'browser-ie-mobile'    => 'InternetExplorer Mobile',
                    'browser-firefox'      => 'Firefox',
                    'browser-firefox-3'    => 'Firefox-3',
                    'browser-firefox-4'    => 'Firefox-4',
                    'browser-firefox-5'    => 'Firefox-5',
                    'browser-firefox-6'    => 'Firefox-6',
                    'browser-firefox-7'    => 'Firefox-7',
                    'browser-firefox-8'    => 'Firefox-8',
                    'browser-firefox-9'    => 'Firefox-9',
                    'browser-firefox-10'   => 'Firefox-10',
                    'browser-firefox-11'   => 'Firefox-11',
                    'browser-firefox-12'   => 'Firefox-12',
                    'browser-chrome'       => 'Chrome',
                    'browser-chrome-10'    => 'Chrome-10',
                    'browser-chrome-11'    => 'Chrome-11',
                    'browser-chrome-12'    => 'Chrome-12',
                    'browser-chrome-13'    => 'Chrome-13',
                    'browser-chrome-14'    => 'Chrome-14',
                    'browser-chrome-15'    => 'Chrome-15',
                    'browser-chrome-16'    => 'Chrome-16',
                    'browser-chrome-17'    => 'Chrome-17',
                    'browser-chrome-18'    => 'Chrome-18',
                    'browser-chrome-19'    => 'Chrome-19',
                    'browser-omniweb'      => 'OmniWeb',
                    'browser-safari'       => 'Safari',
                    'browser-safari-4'     => 'Safari 4',
                    'browser-safari-5'     => 'Safari 5',
                    'browser-opera'        => 'Opera',
                    'browser-opera-mini'   => 'Opera Mini',
                    'browser-opera-mobile' => 'Opera Mobile',
                    'browser-camino'       => 'Camino',
                    'browser-konqueror'    => 'Konqueror',
                    'browser-other'        => 'Other'
                ),
                'Other'   => array
                (
                    '@mobile' => 'Mobile Client'
                )
            ),
            'eval'      => array('multiple'=> true),
            'sql'       => "blob NULL"
        ),
        'filterInvert'                          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filterInvert'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'editor_integration'                    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editor_integration'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'options'   => array('default', 'newsletter', 'flash'),
            'reference' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editors'],
            'eval'      => array('multiple'=> true),
            'sql'       => "blob NULL"
        ),
        'force_editor_integration'              => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['force_editor_integration'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'asseticFilter'                         => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['asseticFilter'],
            'inputType'        => 'select',
            'options_callback' => array('ThemePlus\DataContainer\Stylesheet', 'getAsseticFilterOptions'),
            'reference'        => &$GLOBALS['TL_LANG']['assetic'],
            'eval'             => array('includeBlankOption' => true),
            'sql'              => "varbinary(32) NOT NULL default ''"
        ),
    )
);
