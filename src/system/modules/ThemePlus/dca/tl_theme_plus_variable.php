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
		'enableVersioning'            => true
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

/**
 * Class tl_theme_plus_variable
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_theme_plus_variable extends Backend
{
	private static $objTheme = false;

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();

		$GLOBALS['TL_CSS'][]        = 'plugins/mootools/rainbow.css?' . MOO_RAINBOW . '|screen';
		$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/mootools/rainbow.js?' . MOO_RAINBOW;

		$this->import('BackendUser', 'User');
	}


	/**
	 * Get the variable name.
	 */
	public function getName($varValue, $dc)
	{
		$varValue = standardize($varValue);

		$objVariable = $this->Database
			->prepare("SELECT * FROM tl_theme_plus_variable WHERE id!=? AND pid=? AND name=?")
			->execute($dc->id, $dc->activeRecord->pid, $varValue);
		if ($objVariable->next()) {
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['unique'], $GLOBALS['TL_LANG']['tl_theme_plus_variable']['name'][0]));
		}

		return $varValue;
	}


	/**
	 * Return the color picker wizard
	 *
	 * @param object
	 *
	 * @return string
	 */
	public function colorPicker(DataContainer $dc)
	{
		return ' ' . $this->generateImage('pickcolor.gif', $GLOBALS['TL_LANG']['MSC']['colorpicker'], 'style="vertical-align:top; cursor:pointer;" id="moo_' . $dc->field . '" class="mooRainbow"');
	}


	/**
	 * Check permissions to edit the table
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin) {
			return;
		}

		if (!$this->User->hasAccess('theme_plus', 'themes')) {
			$this->log('Not enough permissions to access the style sheets module', 'tl_theme_plus_variable checkPermission', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
	}


	/**
	 * List an variable
	 *
	 * @param array
	 *
	 * @return string
	 */
	public function listVariables($row)
	{
		$this->import('ThemePlus');

		$label = '<strong>' . $row['name'] . '</strong>: ' . $this->ThemePlus->renderVariable($row);

		switch ($row['type']) {
			case 'text':
				$image = 'system/modules/theme_plus/assets/images/text.png';
				break;

			case 'url':
				$image = 'system/modules/theme_plus/assets/images/url.png';
				break;

			case 'file':
				$image = 'files.gif';
				break;

			case 'color':
				$image = 'system/modules/theme_plus/assets/images/color.png';
				break;

			case 'size':
				$image = 'system/modules/theme_plus/assets/images/size.png';
				break;

			default:
				$image = '';
		}

		if ($image) {
			$image = $this->generateImage(
				$image,
				$GLOBALS['TL_LANG']['tl_theme_plus_variable'][$row['type']][0],
				'style="vertical-align:middle" title="' . specialchars($GLOBALS['TL_LANG']['tl_theme_plus_variable'][$row['type']][0]) . '"')
				. ' ';
		}

		return '<div>' . $image . $label . "</div>\n";

	}
}
