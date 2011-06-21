<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalJavaScriptFile
 */
class ExternalJavaScriptFile {
	
	public function __construct($strUrl)
	{
		parent::__construct($strUrl);
	}


	public function getIncludeHtml()
	{
		// get the file
		$strFile = $this->getFile();
		
		// return html code
		return '<link type="text/css" rel="stylesheet" href="' . specialchars($strFile) . '" />';
	}
	
}

?>