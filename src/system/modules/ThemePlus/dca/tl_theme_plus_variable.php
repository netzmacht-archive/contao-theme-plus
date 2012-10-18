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
 * Table tl_theme_plus_variable
 */
$GLOBALS['TL_DCA']['tl_theme_plus_variable'] = array
(

	// Config
	'config'      => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_theme',
		'enableVersioning'            => true,
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
	'list'        => array
	(
		'sorting'           => array
		(
			'mode'                    => 4,
			'flag'                    => 4,
			'fields'                  => array('name'),
			'panelLayout'             => 'filter;limit',
			'headerFields'            => array('name', 'author', 'tstamp'),
			'child_record_callback'   => array('tl_theme_plus_variable', 'listVariables'),
			'child_record_class'      => 'no_padding'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'   => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy'   => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut'    => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show'   => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes'    => array
	(
		'__selector__'                => array('type'),
		'default'                     => '{variable_legend},type',
		'text'                        => '{variable_legend},name,type,text',
		'url'                         => '{variable_legend},name,type,url',
		'file'                        => '{variable_legend},name,type,file',
		'color'                       => '{variable_legend},name,type,color',
		'size'                        => '{variable_legend},name,type,size'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'restrictLayout'              => 'layout'
	),

	// Fields
	'fields'      => array
	(
		'type'  => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['type'],
			'default'                 => 'text',
			'inputType'               => 'select',
			'filter'                  => true,
			'options'                 => array('text', 'url', 'file', 'color', 'size'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_theme_plus_variable'],
			'eval'                    => array('submitOnChange'=> true)
		),
		'name'  => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['name'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=> true,
			                                   'maxlength'=> 255,
			                                   'rgxp'     => 'alnum'),
			'save_callback'           => array(array('tl_theme_plus_variable', 'getName'))
		),
		'text'  => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['text'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'     => true,
			                                   'maxlength'     => 255,
			                                   'decodeEntities'=> true)
		),
		'url'   => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['url'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'     => true,
			                                   'rgxp'          => 'url',
			                                   'decodeEntities'=> true,
			                                   'tl_class'      => 'clr long')
		),
		'file'  => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['file'],
			'inputType'               => 'fileTree',
			'eval'                    => array('mandatory' => true,
			                                   'fieldType' => 'radio',
			                                   'files'     => true,
			                                   'extensions'=> 'css,jpg,jpeg,png,gif,bmp,svg')
		),
		'color' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['color'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'     => true,
			                                   'maxlength'     => 6,
			                                   'isHexColor'    => true,
			                                   'decodeEntities'=> true,
			                                   'tl_class'      => 'wizard'),
			'wizard'                  => array(array('tl_theme_plus_variable', 'colorPicker'))
		),
		'size'  => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_variable']['size'],
			'inputType'               => 'trbl',
			'options'                 => array('px', '%', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
			'eval'                    => array('includeBlankOption'=> true,
			                                   'rgxp'              => 'digit')
		)
	)
);
