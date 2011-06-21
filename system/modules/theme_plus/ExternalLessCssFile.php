<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalLessCssFile
 */
class ExternalLessCssFile extends ExternalCssFile {
	
	/**
	 * Create a new css file object.
	 */
	public function __construct($strUrl, $strMedia)
	{
		parent::__construct($strUrl, $strMedia);
		
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
		return '<link type="text/css" rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />';
	}
	
}

?>