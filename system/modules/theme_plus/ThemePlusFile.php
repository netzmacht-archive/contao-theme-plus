<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ThemePlusFile
 */
abstract class ThemePlusFile extends System
{
	/**
	 * The conditional comment.
	 * 
	 * @var string
	 */
	protected $strCc;
	
	
	/**
	 * Create a new Theme+ file
	 */
	public function __construct($strCc)
	{
		$this->strCc = trim($strCc);
	}
	
	
	/**
	 * Return the conditional comment.
	 */
	public function getCc()
	{
		return $this->strCc;
	}
	
	
	/**
	 * Get a code that is compatible with TL_CSS and TL_JAVASCRIPT
	 * 
	 * @return string
	 */
	public abstract function getGlobalVariableCode();
	
	
	/**
	 * Get embeded html code
	 * 
	 * @return string
	 */
	public abstract function getEmbededHtml();
	
	
	/**
	 * Get included html code
	 * 
	 * @return string
	 */
	public abstract function getIncludeHtml();
	
	
	/**
	 * Gives the information, if this file can be aggregated.
	 * 
	 * @return true
	 */
	public function isAggregateable()
	{
		return strlen($this->strCc) ? false : true;
	}
	
	
	public function __get($k)
	{
		switch ($k)
		{
		case 'cc':
			return $this->getCc();
			
		case 'globalVariable':
			return $this->getGlobalVariableCode();
			
		case 'embed':
			return $this->getEmbededHtml();
		
		case 'include':
			return $this->getIncludeHtml();
		
		case 'aggregate':
			return $this->isAggregateable();
		}
	}
	
	
	/**
	 * Wrap the conditional comment arround.
	 */
	protected function wrapCc($strCode)
	{
		if (strlen($this->strCc))
		{
			return '<!--[if ' . $this->strCc . ']>' . $strCode . '<![endif]-->';
		}
		return $strCode;
	}
}

?>