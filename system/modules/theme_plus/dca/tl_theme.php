<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_theme_plus_file';
$GLOBALS['TL_DCA']['tl_theme']['list']['operations']['theme_plus_file'] = array
(
	'label'               => &$GLOBALS['TL_LANG']['tl_theme']['theme_plus_file'],
	'href'                => 'table=tl_theme_plus_file',
	'icon'                => 'system/modules/theme_plus/html/icon.png',
	'button_callback'     => array('tl_theme_plus', 'editThemePlusFile')
);


/**
 * Class tl_theme_plus
 *
 */
class tl_theme_plus extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	
	public function editThemePlusFile($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('theme_plus', 'themes')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)).' ';
	}
}
?>