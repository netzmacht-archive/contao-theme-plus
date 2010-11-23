<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Table tl_additional_source
 */
$GLOBALS['TL_DCA']['tl_additional_source'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_theme',
		'enableVersioning'            => true
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sorting'),
			'panelLayout'             => 'filter;limit',
			'headerFields'            => array('name', 'author', 'tstamp'),
			'child_record_callback'   => array('tl_additional_source', 'listAdditionalSource'),
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
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_additional_source']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_additional_source']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_additional_source']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_additional_source']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_additional_source']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type', 'restrictLayout'),
		'default'                     => '{source_legend},type',
		'js_file'                     => '{source_legend},type,cc,js_file;{restrict_legend:hide},restrictLayout;{compress_legend:hide},compress_yui,compress_gz,compress_outdir',
		'js_url'                      => '{source_legend},type,cc,js_url;{restrict_legend:hide},restrictLayout',
		'css_file'                    => '{source_legend},type,cc,css_file,media,editor_integration;{restrict_legend:hide},restrictLayout;{compress_legend:hide},compress_yui,compress_gz,compress_outdir',
		'css_url'                     => '{source_legend},type,cc,css_url,media,editor_integration;{restrict_legend:hide},restrictLayout'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'restrictLayout'              => 'layout'
	),
	
	// Fields
	'fields' => array
	(
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['type'],
			'default'                 => 'js_file',
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => array('js_file','js_url','css_file','css_url'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_additional_source'],
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50')
		),
		'js_file' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['js_file'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio', 'files'=>true, 'extensions'=>'js', 'path'=>$GLOBALS['TL_CONFIG']['uploadPath'], 'tl_class'=>'clr')
		),
		'js_url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['js_url'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr')
		),
		'css_file' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['css_file'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio', 'files'=>true, 'extensions'=>'css', 'path'=>$GLOBALS['TL_CONFIG']['uploadPath'], 'tl_class'=>'clr')
		),
		'css_url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['css_url'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr')
		),
		'cc' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['cc'],
			'inputType'               => 'select',
			'exclude'                 => true,
			'filter'                  => true,
			'options'                 => array('if IE', 'if IE 6', 'if lt IE 6', 'if lte IE 6', 'if gt IE 6', 'if gte IE 6', 'if IE 7', 'if lt IE 7', 'if lte IE 7', 'if gt IE 7', 'if gte IE 7', 'if IE 8', 'if lt IE 8', 'if lte IE 8', 'if gt IE 8', 'if gte IE 8'),
			'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50')
		),
		'media' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['media'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'filter'                  => true,
			'options'                 => array('all', 'aural', 'braille', 'embossed', 'handheld', 'print', 'projection', 'screen', 'tty', 'tv'),
			'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
		),
		'restrictLayout' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['restrictLayout'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true)
		),
		'layout' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['layout'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_additional_source', 'getPageLayouts'),
			'eval'                    => array('multiple'=>'true', 'mandatory'=>true)
		),
		'compress_yui' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['compress_yui'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50')
		),
		'compress_gz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['compress_gz'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50')
		),
		'compress_outdir' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['compress_outdir'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('fieldType'=>'radio', 'files'=>false, 'path'=>'', 'tl_class'=>'clr')
		),
		'editor_integration' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_additional_source']['editor_integration'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'options'                 => array('default', 'newsletter', 'flash'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_additional_source']['editors'],
			'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
		)
	)
);

/**
 * Class tl_additional_source
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2010
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_additional_source extends Backend
{
	private static $objTheme = false;
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	public function detectTheme() {
		if (self::$objTheme === false) {
			$intPid = $this->Input->get('id');
			
			if ($this->Input->get('act')) {
				$objAdditionalSource = $this->Database->prepare("SELECT * FROM tl_additional_source WHERE id=?")
													  ->execute($intPid);
				if ($objAdditionalSource->next()) {
					$intPid = $objAdditionalSource->pid;
				} else {
					$intPid = 0;
				}
			}
			
			$objTheme = $this->Database->prepare("SELECT * FROM tl_theme WHERE id=?")
									   ->execute($intPid);
			if ($objTheme->next()) {
				self::$objTheme = $objTheme;
			}
		}
		return self::$objTheme;
	}
	
	
	/**
	 * Return all page layouts grouped by theme
	 * @return array
	 */
	public function getPageLayouts()
	{
		$objTheme = $this->detectTheme();
		
		$stmtLayout = $this->Database->prepare("
			SELECT l.id, l.name, t.name AS theme
			FROM tl_layout l
			LEFT JOIN tl_theme t ON l.pid=t.id
			WHERE l.pid=?
			ORDER BY t.name, l.name");
		$objLayout = $stmtLayout->execute($objTheme->id);

		if ($objLayout->numRows < 1)
		{
			return array();
		}

		$return = array();

		while ($objLayout->next())
		{
			$return[$objLayout->id] = $objLayout->name;
		}

		return $return;
	}
	

	/**
	 * Check permissions to edit the table
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		if (!$this->User->hasAccess('additional_source', 'themes'))
		{
			$this->log('Not enough permissions to access the style sheets module', 'tl_additional_sources checkPermission', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
	}


	/**
	 * List an additional source
	 * @param array
	 * @return string
	 */
	public function listAdditionalSource($row)
	{
		$label = $row[$row['type']];
		
		if ($row['compress_yui']) {
			$label .= '<span style="color: #009;">.yui</span>';
		}
		
		if ($row['compress_gz']) {
			$label .= '<span style="color: #009;">.gz</span>';
		}
		
		$objTheme = $this->detectTheme();
		if ($objTheme) {
			$folders = unserialize($objTheme->folders);
			if (is_array($folders)) {
				foreach ($folders as $folder) {
					$label = str_replace(
						$folder.'/',
						'<span style="color: #B3B3B3;">'.$folder.'/'.'</span>',
						$label);
				}
			}
		}
		
		if (strlen($row['cc'])) {
			$label .= ' <span style="color: #B3B3B3;">[' . $row['cc'] . ']</span>';
		}
		
		if (strlen($row['media'])) {
			$row['media'] = unserialize($row['media']);
			if (count($row['media'])) {
				$label .= ' <span style="color: #B3B3B3;">[' . implode(', ', $row['media']) . ']</span>';
			}
		}
		
		switch ($row['type']) {
		case 'js_file': case 'js_url':
			$image = 'iconJS.gif';
			break;
		
		case 'css_file': case 'css_url':
			$image = 'iconCSS.gif';
			break;
		
		default:
			$image = false;
			if (isset($GLOBALS['TL_HOOKS']['getAdditionalSourceIconImage']) && is_array($GLOBALS['TL_HOOKS']['getAdditionalSourceIconImage']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getAdditionalSourceIconImage'] as $callback)
				{
					$this->import($callback[0]);
					$image = $this->$callback[0]->$callback[1]($row);
					if ($image !== false) {
						break;
					}
				}
			}
		}
		
		return '<div>' . ($image ? $this->generateImage($image, $label, 'style="vertical-align:middle"') . ' ' : '') . $label ."</div>\n";
		
	}


	/**
	 * Return the edit header button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || count(preg_grep('/^tl_additional_source::/', $this->User->alexf)) > 0) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : '';
	}
}

$this->import('tl_additional_source');
$objTheme = $this->tl_additional_source->detectTheme();
if ($objTheme) {
	$folders = unserialize($objTheme->folders);
	if (is_array($folders) && count($folders)) {
		$GLOBALS['TL_DCA']['tl_additional_source']['fields']['js_file']['eval']['path'] = $folders[0];
		$GLOBALS['TL_DCA']['tl_additional_source']['fields']['css_file']['eval']['path'] = $folders[0];
	}
}

?>