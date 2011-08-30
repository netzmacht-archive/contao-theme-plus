<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalJavaScriptFile
 */
class ExternalJavaScriptFile extends ExternalThemePlusFile
{
	public function __construct($strUrl, $strCc = '')
	{
		parent::__construct($strUrl, $strCc);
	}


	public function getIncludeHtml()
	{
		global $objPage;

		// get the file
		$strFile = $this->getUrl();

		// return html code
		return $this->getDebugComment() . $this->wrapCc('<script' . (($objPage->outputFormat == 'xhtml') ? ' type="text/javascript"' : '') . ' src="' . specialchars($strFile) . '"></script>');
	}


	/**
	 * Convert into a string.
	 */
	public function __toString()
	{
		return $this->getUrl() . '|' . $this->getCc();
	}
}

?>