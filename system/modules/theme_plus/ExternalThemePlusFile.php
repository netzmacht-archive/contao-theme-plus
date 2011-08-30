<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ExternalThemePlusFile
 */
abstract class ExternalThemePlusFile extends ThemePlusFile
{
	/**
	 * Get a file from path. 
	 */
	public static function create()
	{
		$args = func_get_args();
		$strUrl = $args[0];
		$strFile = parse_url($strUrl, PHP_URL_PATH);
		$strExtension = preg_replace('#.*\.(\w+)$#', '$1', $strFile);
		if ($strExtension)
		{
			switch (strtolower($strExtension))
			{
				case 'js':
					return new ExternalJavaScriptFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : false);
				
				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less'])
					{
						return new ExternalCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
					}
					
				case 'less':
					return new ExternalLessCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
			}
		}
		return false;
	}


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