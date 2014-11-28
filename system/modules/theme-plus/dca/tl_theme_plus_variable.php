<?php

/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */


/**
 * Table tl_theme_plus_variable
 */
$GLOBALS['TL_DCA']['tl_theme_plus_variable'] = [
    // Config
    'config'      => [
        'dataContainer'    => 'Table',
        'ptable'           => 'tl_theme',
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index'
            ]
        ],
    ],
    // List
    'list'        => [
        'sorting'           => [
            'mode'                  => 4,
            'flag'                  => 4,
            'fields'                => ['name'],
            'panelLayout'           => 'filter;limit',
            'headerFields'          => ['name', 'author', 'tstamp'],
            'child_record_callback' => ['Bit3\Contao\ThemePlus\DataContainer\Variable', 'listVariables'],
            'child_record_class'    => 'no_padding'
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ]
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['copy'],
                'href'  => 'act=paste&amp;mode=copy',
                'icon'  => 'copy.gif'
            ],
            'cut'    => [
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\')) return false; Backend.getScrollOffset();"'
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ]
        ]
    ],
    // Palettes
    'palettes'    => [
        '__selector__' => ['type'],
        'default'      => '{variable_legend},type',
        'text'         => '{variable_legend},name,type,text',
        'url'          => '{variable_legend},name,type,url',
        'file'         => '{variable_legend},name,type,file',
        'color'        => '{variable_legend},name,type,color',
        'size'         => '{variable_legend},name,type,size'
    ],
    // Subpalettes
    'subpalettes' => [
        'restrictLayout' => 'layout'
    ],
    // Fields
    'fields'      => [
        'id'     => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'    => [
            'foreignKey' => 'tl_style_sheet.name',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'type'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['type'],
            'default'   => 'text',
            'inputType' => 'select',
            'filter'    => true,
            'options'   => ['text', 'url', 'file', 'color', 'size'],
            'reference' => &$GLOBALS['TL_LANG']['tl_theme_plus_variable'],
            'eval'      => ['submitOnChange' => true],
            'sql'       => 'varchar(32) NOT NULL default \'\'',
        ],
        'name'   => [
            'label'         => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['name'],
            'inputType'     => 'text',
            'eval'          => [
                'mandatory' => true,
                'maxlength' => 255,
                'rgxp'      => 'alnum'
            ],
            'save_callback' => [['Bit3\Contao\ThemePlus\DataContainer\Variable', 'getName']],
            'sql'           => 'varchar(255) NOT NULL default \'\'',
        ],
        'text'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['text'],
            'inputType' => 'text',
            'eval'      => [
                'mandatory'      => true,
                'maxlength'      => 255,
                'decodeEntities' => true
            ],
            'sql'       => 'varchar(255) NOT NULL default \'\'',
        ],
        'url'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['url'],
            'inputType' => 'text',
            'eval'      => [
                'mandatory'      => true,
                'rgxp'           => 'url',
                'decodeEntities' => true,
                'tl_class'       => 'clr long'
            ],
            'sql'       => 'text NULL',
        ],
        'file'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['file'],
            'inputType' => 'fileTree',
            'eval'      => [
                'mandatory'  => true,
                'fieldType'  => 'radio',
                'files'      => true,
                'extensions' => 'css,jpg,jpeg,png,gif,bmp,svg'
            ],
            'sql'       => "binary(16) NULL",
        ],
        'color'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['color'],
            'inputType' => 'text',
            'eval'      => [
                'mandatory'      => true,
                'maxlength'      => 6,
                'colorpicker'    => true,
                'isHexColor'     => true,
                'decodeEntities' => true,
                'tl_class'       => 'wizard'
            ],
            'sql'       => 'varchar(6) NOT NULL default \'\'',
        ],
        'size'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['size'],
            'inputType' => 'trbl',
            'options'   => ['px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'],
            'eval'      => [
                'includeBlankOption' => true,
                'rgxp'               => 'digit'
            ],
            'sql'       => 'varchar(128) NOT NULL default \'\'',
        ]
    ]
];
