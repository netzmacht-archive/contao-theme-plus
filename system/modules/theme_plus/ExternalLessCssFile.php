<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalLessCssFile
 */
class ExternalLessCssFile extends ExternalCssFile {
	
	/**
	 * Create a new css file object.
	 */
	public function __construct($strUrl, $strMedia = '', $strCc = '')
	{
		parent::__construct($strUrl, $strMedia, $strCc);
		
		// import the Theme+ master class
		$this->import('ThemePlus');
	}


	public function getIncludeHtml()
	{
		// add client side javascript
		if ($this->ThemePlus->getBELoginStatus())
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
		return $this->wrapCc('<link type="text/css" rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />');
	}
	
}

?>