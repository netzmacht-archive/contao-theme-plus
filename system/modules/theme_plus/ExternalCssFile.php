<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalCssFile
 */
class ExternalCssFile extends ExternalThemePlusFile {
	
	/**
	 * The media selectors.
	 */
	protected $strMedia;
	
	
	public function __construct($strUrl, $strMedia)
	{
		parent::__construct($strUrl);
		$this->strMedia = $strMedia;
	}


	public function getIncludeHtml()
	{
		// get the file
		$strUrl = $this->getUrl();
		
		// return html code
		return '<link type="text/css" rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />';
	}
	
}

?>