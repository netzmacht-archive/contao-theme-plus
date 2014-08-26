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
else if ($session->get('THEME_PLUS_LAST_CSS_TYPE')) {
	$type = $session->get('THEME_PLUS_LAST_CSS_TYPE');
}
else {
	$type = '';
}

$this->loadLanguageFile('tl_theme_plus_filter');

/**
 * Table tl_theme_plus_stylesheet
 */
$GLOBALS['TL_DCA']['tl_theme_plus_stylesheet'] = [
	// Config
	'config'          => [
		'dataContainer'    => 'Table',
		'ptable'           => 'tl_theme',
		'enableVersioning' => true,
		'onload_callback'  => [
			['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'changeFileSource']
		],
		'sql'              => [
			'keys' => [
				'id'  => 'primary',
				'pid' => 'index'
			]
		],
	],
	// List
	'list'            => [
		'sorting'           => [
			'mode'                  => 4,
			'flag'                  => 11,
			'fields'                => ['sorting'],
			'panelLayout'           => 'filter;limit',
			'headerFields'          => ['name', 'author', 'tstamp'],
			'child_record_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'listFile'],
			'child_record_class'    => 'no_padding'
		],
		'global_operations' => [
			'newFile' => [
				'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newFile'],
				'href'       => 'act=paste&mode=create&type=file',
				'class'      => 'header_new_file',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			],
			'newUrl'  => [
				'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newUrl'],
				'href'       => 'act=paste&mode=create&type=url',
				'class'      => 'header_new_url',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			],
			'newCode' => [
				'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['newCode'],
				'href'       => 'act=paste&mode=create&type=code',
				'class'      => 'header_new_code',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			],
			'all'     => [
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			]
		],
		'operations'        => [
			'edit'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			],
			'copy'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['copy'],
				'href'  => 'act=paste&amp;mode=copy',
				'icon'  => 'copy.gif'
			],
			'cut'    => [
				'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['cut'],
				'href'       => 'act=paste&amp;mode=cut',
				'icon'       => 'cut.gif',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			],
			'delete' => [
				'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			],
			'show'   => [
				'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			]
		]
	],
	// Palettes
	'palettes'        => [
		'__selector__' => ['type', 'filter']
	],
	// Meta palettes
	'metapalettes'    => [
		'default' => [
			'source' => ['type']
		],
		'file'    => [
			'source'  => ['type', 'filesource'],
			'file'    => ['file'],
			'layouts' => ['layouts'],
			'filter'  => [':hide', 'cc', 'filter'],
			'editor'  => [':hide', 'editor_integration', 'force_editor_integration'],
			'assetic' => [':hide', 'asseticFilter'],
			'expert'  => [':hide', 'inline'],
		],
		'url'     => [
			'source'  => ['type'],
			'file'    => ['url', 'fetchUrl'],
			'layouts' => ['layouts'],
			'filter'  => [':hide', 'cc', 'filter'],
			'editor'  => [':hide', 'editor_integration', 'force_editor_integration'],
			'assetic' => [':hide', 'asseticFilter'],
			'expert'  => [':hide', 'inline'],
		],
		'code'    => [
			'source'  => ['type', 'code_snippet_title'],
			'file'    => ['code'],
			'layouts' => ['layouts'],
			'filter'  => [':hide', 'cc', 'filter'],
			'editor'  => [':hide', 'editor_integration', 'force_editor_integration'],
			'assetic' => [':hide', 'asseticFilter'],
			'expert'  => [':hide', 'inline'],
		],
	],
	// Meta sub palettes
	'metasubpalettes' => [
		'filter' => ['filterRule'],
	],
	// Meta sub-select palettes
	'metasubselectpalettes' => [
		'inline' => [
			'' => ['standalone'],
		],
	],
	// Fields
	'fields'          => [
		'id'                       => [
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		],
		'pid'                      => [
			'foreignKey' => 'tl_style_sheet.name',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => ['type' => 'belongsTo', 'load' => 'lazy']
		],
		'sorting'                  => [
			'sql' => "int(10) unsigned NOT NULL default '0'"
		],
		'tstamp'                   => [
			'sql' => "int(10) unsigned NOT NULL default '0'"
		],
		'type'                     => [
			'label'         => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['type'],
			'default'       => $type,
			'inputType'     => 'select',
			'filter'        => true,
			'options'       => ['file', 'url', 'code'],
			'reference'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet'],
			'eval'          => [
				'includeBlankOption' => true,
				'submitOnChange'     => true,
				'tl_class'           => 'w50'
			],
			'save_callback' => [['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'rememberType']],
			'sql'           => "varchar(32) NOT NULL default ''"
		],
		'filesource'               => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filesource'],
			'default'   => $GLOBALS['TL_CONFIG']['uploadPath'],
			'inputType' => 'select',
			'filter'    => true,
			'options'   => [$GLOBALS['TL_CONFIG']['uploadPath'], 'assets', 'system/modules', 'composer/vendor'],
			'eval'      => [
				'submitOnChange' => true,
				'tl_class'       => 'w50'
			],
			'sql'       => "varchar(32) NOT NULL default '{$GLOBALS['TL_CONFIG']['uploadPath']}'"
		],
		'code_snippet_title'       => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['code_snippet_title'],
			'inputType' => 'text',
			'eval'      => [
				'mandatory' => true,
				'unique'    => true,
				'maxlength' => 255,
				'tl_class'  => 'w50'
			],
			'sql'       => "varchar(255) NOT NULL default ''"
		],
		'file'                     => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['file'],
			'inputType' => 'fileTree',
			'eval'      => [
				'mandatory'  => true,
				'fieldType'  => 'radio',
				'files'      => true,
				'filesOnly'  => true,
				'extensions' => 'css,less,scss,sass'
			],
			'sql'       => "blob NULL"
		],
		'url'                      => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['url'],
			'inputType' => 'text',
			'eval'      => [
				'mandatory'      => true,
				'decodeEntities' => true,
				'tl_class'       => 'long'
			],
			'sql'       => "blob NULL"
		],
		'fetchUrl'                 => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['fetchUrl'],
			'inputType' => 'checkbox',
			'eval'      => [],
			'sql'       => "char(1) NOT NULL default ''"
		],
		'code'                     => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['code'],
			'inputType' => 'textarea',
			'eval'      => [
				'mandatory'    => true,
				'allowHtml'    => true,
				'preserveTags' => true,
				'class'        => 'monospace',
				'rte'          => version_compare(VERSION, '3.3', '<') ? 'codeMirror|css' : 'ace|css',
				'helpwizard'   => true
			],
			'sql'       => "blob NULL"
		],
		'layouts'                  => [
			'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['layouts'],
			'exclude'          => true,
			'inputType'        => 'checkbox',
			'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\JavaScript', 'listLayouts'],
			'eval'             => ['multiple' => true, 'doNotSaveEmpty' => true],
			'load_callback'    => [['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'loadLayouts']],
			'save_callback'    => [['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'saveLayouts']],
		],
		'cc'                       => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['cc'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => ['tl_class' => 'long'],
			'sql'       => "blob NULL"
		],
		'filter'                   => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filter'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => ['submitOnChange' => true],
			'sql'       => "char(1) NOT NULL default ''"
		],
		'filterRule'               => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['filterRule'],
			'exclude'   => true,
			'inputType' => 'multiColumnWizard',
			'eval'      => [
				'columnFields' => [
					'platform'        => [
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['platform'],
						'exclude'   => true,
						'inputType' => 'select',
						'options'   => [
							'desktop'          => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['desktop'],
							'tablet-or-mobile' => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['tablet-or-mobile'],
							'tablet'           => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['tablet'],
							'mobile'           => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['mobile'],
						],
						'eval'      => [
							'includeBlankOption' => true,
							'style'              => 'width:180px',
						]
					],
					'system'          => [
						'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['system'],
						'exclude'          => true,
						'inputType'        => 'select',
						'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'getSystems'],
						'eval'             => [
							'style'              => 'width:158px',
							'includeBlankOption' => true
						]
					],
					'browser'         => [
						'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['browser'],
						'exclude'          => true,
						'inputType'        => 'select',
						'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'getBrowsers'],
						'eval'             => [
							'style'              => 'width:158px',
							'includeBlankOption' => true
						]
					],
					'comparator'      => [
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['comparator'],
						'inputType' => 'select',
						'options'   => [
							'lt'  => '<',
							'lte' => '<=',
							'gte' => '>=',
							'gt'  => '>'
						],
						'eval'      => [
							'style'              => 'width:70px',
							'includeBlankOption' => true
						]
					],
					'browser_version' => [
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['browser_version'],
						'inputType' => 'text',
						'eval'      => [
							'style' => 'width:70px'
						]
					],
					'invert'          => [
						'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_filter']['invert'],
						'exclude'   => true,
						'inputType' => 'checkbox',
						'eval'      => [
							'style' => 'width:60px'
						]
					]
				]
			],
			'sql'       => "blob NULL"
		],
		'editor_integration'       => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editor_integration'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'options'   => ['default', 'newsletter', 'flash'],
			'reference' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['editors'],
			'eval'      => ['multiple' => true],
			'sql'       => "blob NULL"
		],
		'force_editor_integration' => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['force_editor_integration'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'sql'       => "char(1) NOT NULL default ''"
		],
		'inline'                   => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['inline'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50'],
			'sql'       => "char(1) NOT NULL default ''"
		],
		'standalone'               => [
			'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['standalone'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => ['tl_class' => 'w50'],
			'sql'       => "char(1) NOT NULL default ''"
		],
		'asseticFilter'            => [
			'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet']['asseticFilter'],
			'inputType'        => 'select',
			'options_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Stylesheet', 'getAsseticFilterOptions'],
			'reference'        => &$GLOBALS['TL_LANG']['assetic'],
			'eval'             => ['includeBlankOption' => true],
			'sql'              => "varbinary(32) NOT NULL default ''"
		],
	]
];
