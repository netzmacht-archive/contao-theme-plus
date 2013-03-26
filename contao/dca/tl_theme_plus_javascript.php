<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS']['theme_plus_be'] = 'system/modules/theme-plus/assets/css/be.css';
}

$session = \Session::getInstance();
if (\Input::get('type')) {
	$type = \Input::get('type');
}
else if ($session->get('THEME_PLUS_LAST_JS_TYPE')) {
	$type = $session->get('THEME_PLUS_LAST_JS_TYPE');
}
else {
	$type = '';
}

$this->loadLanguageFile('tl_theme_plus_filter');

/**
 * Table tl_theme_plus_javascript
 */
$GLOBALS['TL_DCA']['tl_theme_plus_javascript'] = array
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
            'newFile'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['newFile'],
                'href'       => 'act=paste&mode=create&type=file',
                'class'      => 'header_new_file',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'newUrl'     => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['newUrl'],
                'href'       => 'act=paste&mode=create&type=url',
                'class'      => 'header_new_url',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'newCode'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['newCode'],
                'href'       => 'act=paste&mode=create&type=code',
                'class'      => 'header_new_code',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'all'        => array
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
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'copy'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['copy'],
                'href'  => 'act=paste&amp;mode=copy',
                'icon'  => 'copy.gif'
            ),
            'cut'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['show'],
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
			'layouts' => array('layouts'),
            'filter'  => array(':hide', 'cc', 'filter'),
            'assetic' => array(':hide', 'asseticFilter'),
            'expert'  => array(':hide', 'position')
        ),
        'url'      => array
        (
            'source'  => array('type'),
            'file'    => array('url', 'fetchUrl'),
			'layouts' => array('layouts'),
            'filter'  => array(':hide', 'cc', 'filter'),
            'assetic' => array(':hide', 'asseticFilter'),
            'expert'  => array(':hide', 'position')
        ),
        'code'     => array
        (
            'source'  => array('type', 'code_snippet_title'),
            'file'    => array('code'),
			'layouts' => array('layouts'),
            'filter'  => array(':hide', 'cc', 'filter'),
            'assetic' => array(':hide', 'asseticFilter'),
            'expert'  => array(':hide', 'position')
        ),
    ),

    // MetaSubpalettes
    'metasubpalettes' => array
    (
        'filter' => array('filterRule')
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
            'label'         => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['type'],
            'default'       => $type,
            'inputType'     => 'select',
            'filter'        => true,
            'options'       => array('file', 'url', 'code'),
            'reference'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript'],
            'eval'          => array('includeBlankOption'=> true,
                                     'submitOnChange'    => true,
                                     'tl_class'          => 'w50'),
            'save_callback' => array(array('ThemePlus\DataContainer\JavaScript', 'rememberType')),
            'sql'           => "varchar(32) NOT NULL default ''"
        ),
        'code_snippet_title'                    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['code_snippet_title'],
            'inputType' => 'text',
            'eval'      => array('mandatory'     => true,
                                 'unique'        => true,
                                 'maxlength'     => 255,
                                 'tl_class'      => 'w50'),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'file'                                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['file'],
            'inputType' => 'fileTree',
            'eval'      => array('mandatory' => true,
                                 'fieldType' => 'radio',
                                 'files'     => true,
                                 'extensions'=> 'js'),
            'sql'       => "int(10) unsigned NOT NULL"
        ),
        'url'                                   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['url'],
            'inputType' => 'text',
            'eval'      => array('mandatory'     => true,
                                 'decodeEntities'=> true,
                                 'tl_class'      => 'long'),
            'sql'       => "blob NULL"
        ),
        'fetchUrl'                                 => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['fetchUrl'],
            'inputType' => 'checkbox',
            'eval'      => array(),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'code'                                  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['code'],
            'inputType' => 'textarea',
            'eval'      => array('mandatory' => true,
                                 'allowHtml' => true,
                                 'class'     => 'monospace',
                                 'rte'       => 'codeMirror|javascript',
                                 'helpwizard'=> true),
            'sql'       => "blob NULL"
        ),
        'position'                              => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['position'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => array('head', 'body'),
            'reference' => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['positions'],
            'eval'      => array('includeBlankOption' => true,
                                 'blankOptionLabel' => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['positions']['inherit']),
            'sql'       => "char(4) NOT NULL default ''"
        ),
        'layouts'                               => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['layouts'],
            'exclude'   => true,
            'inputType' => 'checkbox',
			'options_callback' => array('ThemePlus\DataContainer\JavaScript', 'listLayouts'),
            'eval'      => array('multiple' => true, 'doNotSaveEmpty' => true),
            'load_callback' => array(array('ThemePlus\DataContainer\JavaScript', 'loadLayouts')),
            'save_callback' => array(array('ThemePlus\DataContainer\JavaScript', 'saveLayouts')),
        ),
        'cc'                                    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['cc'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('tl_class'=> 'long'),
            'sql'       => "blob NULL"
        ),
        'filter'                                => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['filter'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange'=> true),
            'sql'       => "char(1) NOT NULL default ''"
        ),
        'filterRule'                            => array
        (
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filterRule'],
			'exclude'   => true,
			'inputType' => 'multiColumnWizard',
			'eval'      => array
			(
				'columnFields' => array
				(
					'system'          => array
					(
						'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['system'],
						'exclude'          => true,
						'inputType'        => 'select',
						'options_callback' => array('ThemePlus\DataContainer\File', 'getSystems'),
						'eval'             => array(
							'style'              => 'width:158px',
							'includeBlankOption' => true
						)
					),
					'browser'         => array
					(
						'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['browser'],
						'exclude'          => true,
						'inputType'        => 'select',
						'options_callback' => array('ThemePlus\DataContainer\File', 'getBrowsers'),
						'eval'             => array(
							'style'              => 'width:158px',
							'includeBlankOption' => true
						)
					),
					'comparator'      => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['comparator'],
						'inputType' => 'select',
						'options'   => array(
							'lt'  => '<',
							'lte' => '<=',
							'gte' => '>=',
							'gt'  => '>'
						),
						'eval'      => array(
							'style'              => 'width:70px',
							'includeBlankOption' => true
						)
					),
					'browser_version' => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['browser_version'],
						'inputType' => 'text',
						'eval'      => array(
							'style' => 'width:70px'
						)
					),
					'platform'        => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['platform'],
						'exclude'   => true,
						'inputType' => 'select',
						'options'   => array(
							'desktop' => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['desktop'],
							'tablet'  => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['tablet'],
							'mobile'  => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['mobile'],
						),
						'eval'      => array(
							'includeBlankOption' => true,
							'style'              => 'width:70px',
						)
					),
					'invert'          => array
					(
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['invert'],
						'exclude'   => true,
						'inputType' => 'checkbox',
						'eval'      => array(
							'style' => 'width:60px'
						)
					)
				)
			),
			'sql'       => "blob NULL"
        ),
        'asseticFilter'                         => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_javascript']['asseticFilter'],
            'inputType'        => 'select',
            'options_callback' => array('ThemePlus\DataContainer\JavaScript', 'getAsseticFilterOptions'),
            'reference'        => &$GLOBALS['TL_LANG']['assetic'],
            'eval'             => array('includeBlankOption' => true),
            'sql'              => "varbinary(32) NOT NULL default ''"
        ),
    )
);
