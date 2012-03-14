<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Table tl_theme_plus_file
 */
$GLOBALS['TL_DCA']['tl_theme_plus_file'] = array
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
			'child_record_callback'   => array('tl_theme_plus_file', 'listFile'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type', 'filter'),
		'default'                     => '{source_legend},type',
		'js_file'                     => '{source_legend},type,js_file,cc,filter',
		'js_url'                      => '{source_legend},type,js_url,cc,filter',
		'css_file'                    => '{source_legend},type,css_file,media,cc,filter;{editor_legend:hide},editor_integration,force_editor_integration',
		'css_url'                     => '{source_legend},type,css_url,media,cc,filter;{editor_legend:hide},editor_integration,force_editor_integration'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'filter'                      => 'filterRule,filterInvert'
	),

	// Fields
	'fields' => array
	(
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['type'],
			'default'                 => 'css_file',
			'inputType'               => 'select',
			'filter'                  => true,
			'options'                 => array('js_file','js_url','css_file','css_url'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file'],
			'eval'                    => array('submitOnChange'=>true)
		),
		'js_file' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_file'],
			'inputType'               => 'fileTree',
			'exclude'                 => true,
			'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio', 'files'=>true, 'extensions'=>'js', 'path'=>$GLOBALS['TL_CONFIG']['uploadPath'])
		),
		'js_url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['js_url'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'long')
		),
		'css_file' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_file'],
			'inputType'               => 'fileTree',
			'exclude'                 => true,
			'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio', 'files'=>true, 'extensions'=>'css,less', 'path'=>$GLOBALS['TL_CONFIG']['uploadPath'])
		),
		'css_url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['css_url'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'long')
		),
		'media' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['media'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'eval'                    => array('tl_class'=>'long', 'decodeEntities'=>true)
		),
		'cc' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['cc'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'eval'                    => array('tl_class'=>'long')
		),
		'filter' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['filter'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'eval'                    => array('submitOnChange'=>true)
		),
		'filterRule' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['filterRule'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'options'                 => array
			(
				'OS' => array
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
					'browser-chrome'       => 'Chrome',
					'browser-chrome-10'    => 'Chrome-10',
					'browser-chrome-11'    => 'Chrome-11',
					'browser-chrome-12'    => 'Chrome-12',
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
				'Other' => array
				(
					'@mobile' => 'Mobile Client'
				)
			),
			'eval'                    => array('multiple'=>true)
		),
		'filterInvert' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['filterInvert'],
			'inputType'               => 'checkbox',
			'exclude'                 => true
		),
		'editor_integration' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['editor_integration'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'options'                 => array('default', 'newsletter', 'flash'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['editors'],
			'eval'                    => array('multiple'=>true)
		),
		'force_editor_integration' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_theme_plus_file']['force_editor_integration'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		)
	)
);

/**
 * Class tl_theme_plus_file
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_theme_plus_file extends Backend
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
				$objThemePlusFile = $this->Database->prepare("SELECT * FROM tl_theme_plus_file WHERE id=?")
													  ->execute($intPid);
				if ($objThemePlusFile->next()) {
					$intPid = $objThemePlusFile->pid;
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

		$stmtLayout = $this->Database->prepare("SELECT
				l.id, l.name, t.name AS theme
			FROM
				tl_layout l
			LEFT JOIN
				tl_theme t
			ON
				l.pid=t.id
			WHERE
				l.pid=?
			ORDER BY
				t.name, l.name");
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

		if (!$this->User->hasAccess('theme_plus', 'themes'))
		{
			$this->log('Not enough permissions to access the style sheets module', 'tl_theme_plus_file checkPermission', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
	}


	/**
	 * List an file
	 * @param array
	 * @return string
	 */
	public function listFile($row)
	{
		$label = $row[$row['type']];

		if (strlen($row['cc'])) {
			$label .= ' <span style="color: #B3B3B3;">[' . $row['cc'] . ']</span>';
		}

		if (strlen($row['media'])) {
			$label .= ' <span style="color: #B3B3B3;">[' . $row['media'] . ']</span>';
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
		return ($this->User->isAdmin || count(preg_grep('/^tl_theme_plus_file::/', $this->User->alexf)) > 0) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : '';
	}
}

?>