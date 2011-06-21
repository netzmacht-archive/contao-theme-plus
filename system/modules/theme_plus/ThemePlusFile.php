<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ThemePlusFile
 */
abstract class ThemePlusFile extends System {

	/**
	 * Get a code that is compatible with TL_CSS and TL_JAVASCRIPT
	 */
	public abstract function getGlobalVariableCode();
	
	
	/**
	 * Get embeded html code
	 */
	public abstract function getEmbededHtml();
	
	
	/**
	 * Get included html code
	 */
	public abstract function getIncludeHtml();
	
	
	/**
	 * Gives the information, if this file can be aggregated.
	 */
	public function isAggregateable()
	{
		return true;
	}
	
	
	public function __get($k)
	{
		switch ($k)
		{
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
	
}

?>