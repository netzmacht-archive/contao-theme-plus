<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class ThemePlusFile
 * 
 * 
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
abstract class ThemePlusFile {

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