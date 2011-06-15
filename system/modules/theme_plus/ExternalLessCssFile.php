<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalLessCssFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class ExternalLessCssFile extends ExternalCssFile {
	
	/**
	 * Create a new css file object.
	 */
	public function __construct($strUrl, $arrMedia)
	{
		parent::__construct($strUrl, $arrMedia);
	}


	public function getIncludeHtml()
	{
		// add client side javascript
		if (ThemePlus::getBELoginStatus())
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.development.js';
		}
		else
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/lesscss/less.min.js';
		}
		
		// get the file
		$strUrl = $this->getUrl();
		
		// return html code
		return '<link type="text/css" rel="stylesheet" href="' . specialchars($strUrl) . '"' . (count($this->arrMedia) ? ' media="' . implode(',', $this->arrMedia) . '"' : '') . ' />';
	}
	
}

?>