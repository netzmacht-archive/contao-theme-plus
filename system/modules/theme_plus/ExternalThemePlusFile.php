<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalThemePlusFile
 */
abstract class ExternalThemePlusFile extends ThemePlusFile {

	/**
	 * The origin file path.
	 */
	protected $strUrl;

	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strUrl, $strCc = '')
	{
		parent::__construct($strCc);
		$this->strUrl = $strUrl;
	}
	
	
	/**
	 * Get a debug comment string
	 */
	protected function getDebugComment()
	{
		$this->import('ThemePlus');
		if ($this->ThemePlus->getBELoginStatus())
		{
			return '<!-- external url: ' . $this->getUrl() . ' -->' . "\n";
		}
		return '';
	}
	
	
	/**
	 * Get the url.
	 */
	public function getUrl()
	{
		return $this->strUrl;
	}
	
	
	public function getGlobalVariableCode()
	{
		return $this->getUrl() . (strlen($this->strCc) ? '|' . $this->strCc : '');
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