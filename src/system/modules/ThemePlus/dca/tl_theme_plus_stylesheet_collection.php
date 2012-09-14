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


\Controller::loadLanguageFile('assetic');


/**
 * Table tl_theme_plus_stylesheet_collection
 */
$GLOBALS['TL_DCA']['tl_theme_plus_stylesheet_collection'] = array
(

    // Config
    'config'       => array
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
        'model'            => 'ThemePlus\Model\StylesheetCollectionModel',
    ),

    // List
    'list'         => array
    (
        'sorting'           => array
        (
            'mode'                  => 4,
            'flag'                  => 4,
            'fields'                => array('name'),
            'panelLayout'           => 'filter;search,limit',
            'headerFields'          => array('name', 'author', 'tstamp'),
            'child_record_callback' => array('ThemePlus\DataContainer\Collection', 'listCollection'),
            'child_record_class'    => 'no_padding'
        ),
        'global_operations' => array
        (
            'all' => array
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
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'copy'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['copy'],
                'href'  => 'act=paste&amp;mode=copy',
                'icon'  => 'copy.gif'
            ),
            'cut'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['cut'],
                'href'       => 'act=paste&amp;mode=cut',
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        )
    ),

    // MetaPalettes
    'metapalettes' => array
    (
        'default' => array
        (
            'collection' => array('name', 'files')
        ),
    ),

    // Fields
    'fields'       => array
    (
        'id'     => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid'    => array
        (
            'foreignKey' => 'tl_style_sheet.name',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => array('type' => 'belongsTo', 'load' => 'lazy')
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'name'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['name'],
            'inputType' => 'text',
            'search'    => true,
            'eval'      => array('mandatory' => true,
                                 'maxlength' => 255),
            'sql'       => "varchar(255) NOT NULL default ''"
        ),
        'files'  => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['files'],
            'inputType'        => 'checkboxWizard',
            'options_callback' => array('ThemePlus\DataContainer\StylesheetCollection', 'getFiles'),
            'eval'             => array('mandatory' => true),
            'sql'              => 'blob NULL'
        ),
        'filter' => array
        (
            'label'            => &$GLOBALS['TL_LANG']['tl_theme_plus_stylesheet_collection']['filter'],
            'inputType'        => 'select',
            'options_callback' => array('ThemePlus\DataContainer\Stylesheet', 'getAsseticFilterOptions'),
            'reference'        => &$GLOBALS['TL_LANG']['assetic'],
            'eval'             => array('mandatory' => true),
            'sql'              => "varbinary(32) NOT NULL default ''"
        ),
    )
);
