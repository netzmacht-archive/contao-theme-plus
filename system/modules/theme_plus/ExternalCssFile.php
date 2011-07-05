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
	
	
	public function __construct($strUrl, $strMedia = '', $strCc = '')
	{
		parent::__construct($strUrl, $strCc);
		$this->strMedia = $strMedia;
	}
	
	
	public function getGlobalVariableCode()
	{
		return $this->getUrl() . (strlen($this->strMedia) ? '|' . $this->strMedia : '') . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}


	public function getIncludeHtml()
	{
		// get the file
		$strUrl = $this->getUrl();
		
		// return html code
		return $this->wrapCc('<link type="text/css" rel="stylesheet" href="' . specialchars($strUrl) . '"' . (strlen($this->strMedia) ? ' media="' . $this->strMedia . '"' : '') . ' />');
	}
	
}

?>