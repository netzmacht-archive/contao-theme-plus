<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalThemePlusFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
abstract class ExternalThemePlusFile extends ThemePlusFile {

	/**
	 * The origin file path.
	 */
	protected $strUrl;

	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strUrl)
	{
		$this->strUrl = $strUrl;
	}
	
	
	/**
	 * Get the url.
	 */
	public function getUrl()
	{
		return $this->strUrl;
	}
	
	
	public function getEmbededHtml()
	{
		return $this->getIncludeHtml();
	}
	
	
	public function isAggregateable()
	{
		return false;
	}
	
	
	public function __get($k)
	{
		switch ($k)
		{
		case 'url':
			return $this->getUrl();
		
		default:
			return parent::__get($k);
		}
	}
	
}

?>