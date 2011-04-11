<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_additional_source';
$GLOBALS['TL_DCA']['tl_theme']['list']['operations']['additional_source'] = array
(
	'label'               => &$GLOBALS['TL_LANG']['tl_theme']['additional_source'],
	'href'                => 'table=tl_additional_source',
	'icon'                => 'system/modules/layout_additional_sources/html/additional_source.png',
	'button_callback'     => array('tl_theme_additional_source', 'editAdditionalSource')
);


/**
 * Class tl_theme_additional_source
 *
 */
class tl_theme_additional_source extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	
	public function editAdditionalSource($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('additional_source', 'themes')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.png$/i', '_.png', $icon)).' ';
	}
}
?>