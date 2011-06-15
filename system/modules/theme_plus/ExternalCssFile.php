<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalCssFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class ExternalCssFile extends ExternalThemePlusFile {
	
	/**
	 * The media selectors.
	 */
	protected $arrMedia;
	
	
	public function __construct($strUrl, $arrMedia)
	{
		parent::__construct($strUrl);
		$this->arrMedia = $arrMedia;
	}


	public function getIncludeHtml()
	{
		// get the file
		$strUrl = $this->getUrl();
		
		// return html code
		return '<link type="text/css" rel="stylesheet" href="' . specialchars($strUrl) . '"' . (count($this->arrMedia) ? ' media="' . implode(',', $this->arrMedia) . '"' : '') . ' />';
	}
	
}

?>